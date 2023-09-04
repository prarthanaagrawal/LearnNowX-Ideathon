<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\LicenseController;
use App\Services\Statistics\UserService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request;
use Orhanerday\OpenAi\OpenAi;
use App\Models\SubscriptionPlan;
use App\Models\FavoriteChat;
use App\Models\ChatHistory;
use App\Models\Chat;
use App\Models\User;


class ChatController extends Controller
{
    private $api;
    private $user;

    public function __construct()
    {
        $this->api = new LicenseController();
        $this->user = new UserService();
    }

    /** 
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {   
        if (session()->has('message_code')) {
            session()->forget('message_code');
        }

        $favorite_chats = Chat::select('chats.*', 'favorite_chats.*')->where('favorite_chats.user_id', auth()->user()->id)->join('favorite_chats', 'favorite_chats.chat_code', '=', 'chats.chat_code')->where('status', true)->orderBy('category', 'asc')->get();    
        $user_chats = FavoriteChat::where('user_id', auth()->user()->id)->pluck('chat_code');     
        $other_chats = Chat::whereNotIn('chat_code', $user_chats)->where('status', true)->orderBy('category', 'asc')->get();                 
        
        return view('user.chat.index', compact('favorite_chats', 'other_chats'));
    }


    /**
	*
	* Process Input Text
	* @param - file id in DB
	* @return - confirmation
	*
	*/
	public function process(Request $request) 
    {       
        # Check if user has access to the chat bot
        $template = Chat::where('chat_code', $request->chat_code)->first();
        if (auth()->user()->group == 'user') {
            if (config('settings.chat_feature_user') == 'allow') {
                if (config('settings.chats_access_user') != 'all') {
                    if ($template->category != config('settings.chats_access_user')) {                         
                        $status = 'error';
                        $message = __('This chat assistant is not available for your account, subscribe to get a proper access');
                        return response()->json(['status' => $status, 'message' => $message]);                     
                    } 
                }                
            } else {
                $status = 'error';
                $message = __('Ai chat assistant feature is not available for free tier users, subscribe to get a proper access');
                return response()->json(['status' => $status, 'message' => $message]);      
            }
        } elseif (auth()->user()->group == 'subscriber') {
            $plan = SubscriptionPlan::where('id', auth()->user()->plan_id)->first();
            if ($plan->chats != 'all' && $plan->chats != 'premium') {          
                if ($plan->chats == 'professional' && $template->category == 'premium') {
                    $status = 'error';
                    $message =  __('Your current subscription does not include support for this chat assitant category');
                    return response()->json(['status' => $status, 'message' => $message]); 
                } else if($plan->chats == 'standard' && ($template->category == 'premium' || $template->category == 'professional')) {
                    $status = 'error';
                    $message =  __('Your current subscription does not include support for this chat assitant category');
                    return response()->json(['status' => $status, 'message' => $message]); 
                }                  
            }
        }

        # Check if user has sufficient words available to proceed
        $balance = auth()->user()->available_words + auth()->user()->available_words_prepaid;
        $words = count(explode(' ', ($request->input('message'))));
        if ($balance <= 0) {
            $status = 'error';
            $message = __('You do not have any words left to proceed with your next chat message request, subscribe or top up to get more words');
            return response()->json(['status' => $status, 'message' => $message]);
        } elseif ($balance < $words) {
            $status = 'error';
            $message = __('You do not have sufficient words left to proceed with your next chat message request, subscribe or top up to get more words');
            return response()->json(['status' => $status, 'message' => $message]);
        }

        $main_chat = Chat::where('chat_code', $request->chat_code)->first();

        if ($request->message_code == '') {
            $messages = ['role' => 'system', 'content' => $main_chat->prompt];            
            $messages[] = ['role' => 'user', 'content' => $request->input('message')];

            $chat = new ChatHistory();
            $chat->user_id = auth()->user()->id;
            $chat->title = 'New Chat';
            $chat->chat_code = $request->chat_code;
            $chat->message_code = strtoupper(Str::random(10));
            $chat->messages = 1;
            $chat->chat = $messages;
            $chat->save();
        } else {
            $chat_message = ChatHistory::where('message_code', $request->message_code)->first();

            if ($chat_message) {

                if (is_null($chat_message->chat)) {
                    $messages[] = ['role' => 'system', 'content' => $main_chat->prompt]; 
                } else {
                    $messages = $chat_message->chat;
                }
                
                array_push($messages, ['role' => 'user', 'content' => $request->input('message')]);
                $chat_message->messages = ++$chat_message->messages;
                $chat_message->chat = $messages;
                $chat_message->save();
            } else {
                $messages[] = ['role' => 'system', 'content' => $main_chat->prompt];            
                $messages[] = ['role' => 'user', 'content' => $request->input('message')];

                $chat = new ChatHistory();
                $chat->user_id = auth()->user()->id;
                $chat->title = 'New Chat';
                $chat->chat_code = $request->chat_code;
                $chat->message_code = $request->message_code;
                $chat->messages = 1;
                $chat->chat = $messages;
                $chat->save();
            }
        }
        

        $request->session()->put('message_code', $request->message_code);

        return response()->json(['status' => 'success', 'old'=> $balance, 'current' => ($balance - $words)]);

	}


     /**
	*
	* Process Chat
	* @param - file id in DB
	* @return - confirmation
	*
	*/
    public function generateChat(Request $request) 
    {
        return response()->stream(function () {

            $open_ai = new OpenAi(config('services.openai.key'));
            $message_code = session()->get('message_code');

            $chat_message = ChatHistory::where('message_code', $message_code)->first();
            $messages = $chat_message->chat;

            $text = "";
            $opts = [
                'model' => 'gpt-3.5-turbo',
                'messages' => $messages,
                'temperature' => 1.0,
                'frequency_penalty' => 0,
                'presence_penalty' => 0,
                'stream' => true
            ];
            
            $complete = $open_ai->chat($opts, function ($curl_info, $data) use (&$text) {
                if ($obj = json_decode($data) and $obj->error->message != "") {
                    error_log(json_encode($obj->error->message));
                } else {
                    echo $data;

                    $clean = str_replace("data: ", "", $data);
                    $first = str_replace("}\n\n{", ",", $clean);
    
                    if(str_contains($first, 'assistant')) {
                        $raw = str_replace('"choices":[{"delta":{"role":"assistant"', '"random":[{"alpha":{"role":"assistant"', $first);
                        $response = json_decode($raw, true);
                    } else {
                        $response = json_decode($clean, true);
                    }    
        
                    if ($data != "data: [DONE]\n\n" and isset($response["choices"][0]["delta"]["content"])) {
                        $text .= $response["choices"][0]["delta"]["content"];
                    }
                }

                echo PHP_EOL;
                ob_flush();
                flush();
                return strlen($data);
            });

            # Update credit balance
            $words = count(explode(' ', ($text)));
            $this->updateBalance($words);  
            
            array_push($messages, ['role' => 'assistant', 'content' => $text]);
            $chat_message->messages = ++$chat_message->messages;
            $chat_message->chat = $messages;
            $chat_message->save();
           
        }, 200, [
            'Cache-Control' => 'no-cache',
            'Content-Type' => 'text/event-stream',
        ]);
        
    }


    /**
	*
	* Clear Session
	* @param - file id in DB
	* @return - confirmation
	*
	*/
	public function clear(Request $request) 
    {
        if (session()->has('message_code')) {
            session()->forget('message_code');
        }

        return response()->json(['status' => 'success']);
	}



    /**
	*
	* Update user word balance
	* @param - total words generated
	* @return - confirmation
	*
	*/
    public function updateBalance($words) {

        $user = User::find(Auth::user()->id);

        if (Auth::user()->available_words > $words) {

            $total_words = Auth::user()->available_words - $words;
            $user->available_words = ($total_words < 0) ? 0 : $total_words;

        } elseif (Auth::user()->available_words_prepaid > $words) {

            $total_words_prepaid = Auth::user()->available_words_prepaid - $words;
            $user->available_words_prepaid = ($total_words_prepaid < 0) ? 0 : $total_words_prepaid;

        } elseif ((Auth::user()->available_words + Auth::user()->available_words_prepaid) == $words) {

            $user->available_words = 0;
            $user->available_words_prepaid = 0;

        } else {

            $remaining = $words - Auth::user()->available_words;
            $user->available_words = 0;

            $prepaid_left = Auth::user()->available_words_prepaid - $remaining;
            $user->available_words_prepaid = ($prepaid_left < 0) ? 0 : $prepaid_left;

        }

        $user->update();

        return true;
    }


    /**
	*
	* Update user word balance
	* @param - total words generated
	* @return - confirmation
	*
	*/
    public function messages(Request $request) {

        if ($request->ajax()) {

            if (session()->has('message_code')) {
                session()->forget('message_code');
            }

            $messages = ChatHistory::where('user_id', auth()->user()->id)->where('message_code', $request->code)->first();
            $message = ($messages) ? json_encode($messages, false) : 'new';
            return $message;
        }   
    }


    /**
	* 
	* Process media file
	* @param - file id in DB
	* @return - confirmation
	* 
	*/
	public function view($code) 
    {
        if (session()->has('message_code')) {
            session()->forget('message_code');
        }

        $chat = Chat::where('chat_code', $code)->first(); 
        $messages = ChatHistory::where('user_id', auth()->user()->id)->where('chat_code', $chat->chat_code)->orderBy('updated_at', 'desc')->get(); 
        $message_chat = ChatHistory::where('user_id', auth()->user()->id)->where('chat_code', $chat->chat_code)->latest('updated_at')->first(); 
        $default_message = ($message_chat) ? json_encode($message_chat, false) : 'new';

        return view('user.chat.view', compact('chat', 'messages', 'default_message'));
	}


    /**
	*
	* Rename chat
	* @param - file id in DB
	* @return - confirmation
	*
	*/
	public function rename(Request $request) 
    {
        if ($request->ajax()) {

            $chat = ChatHistory::where('message_code', request('code'))->first(); 

            if ($chat) {
                if ($chat->user_id == auth()->user()->id){

                    $chat->title = request('name');
                    $chat->save();
    
                    $data['status'] = 'success';
                    $data['code'] = request('code');
                    return $data;  
        
                } else{
    
                    $data['status'] = 'error';
                    $data['message'] = __('There was an error while changing the chat title');
                    return $data;
                }
            } 
              
        }
	}


    /**
	*
	* Delete chat
	* @param - file id in DB
	* @return - confirmation
	*
	*/
	public function delete(Request $request) 
    {
        if ($request->ajax()) {

            $chat = ChatHistory::where('message_code', request('code'))->first(); 

            if ($chat) {
                if ($chat->user_id == auth()->user()->id){

                    $chat->delete();

                    if (session()->has('message_code')) {
                        session()->forget('message_code');
                    }
    
                    $data['status'] = 'success';
                    return $data;  
        
                } else{
    
                    $data['status'] = 'error';
                    $data['message'] = __('There was an error while deleting the chat history');
                    return $data;
                }
            } else {
                $data['status'] = 'empty';
                return $data;
            }
              
        }
	}


     /**
	*
	* Set favorite status
	* @param - file id in DB
	* @return - confirmation
	*
	*/
	public function favorite(Request $request) 
    {
        if ($request->ajax()) {


            $chat = Chat::where('chat_code', request('id'))->first(); 

            $favorite = FavoriteChat::where('chat_code', $chat->chat_code)->where('user_id', auth()->user()->id)->first();

            if ($favorite) {

                $favorite->delete();

                $data['status'] = 'success';
                $data['set'] = true;
                return $data;  
    
            } else{

                $new_favorite = new FavoriteChat();
                $new_favorite->user_id = auth()->user()->id;
                $new_favorite->chat_code = $chat->chat_code;
                $new_favorite->save();

                $data['status'] = 'success';
                $data['set'] = false;
                return $data; 
            }  
        }
	}


    public function escapeJson($value) 
    { 
        $escapers = array("\\", "/", "\"", "\n", "\r", "\t", "\x08", "\x0c");
        $replacements = array("\\\\", "\\/", "\\\"", "\\n", "\\r", "\\t", "\\f", "\\b");
        $result = str_replace($escapers, $replacements, $value);
        return $result;
    }

}
