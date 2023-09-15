![](./images/new.png)

## LearnNowX-Ideathon

# WeCare Generative AI
WeCare is to utilize advanced artificial intelligence techniques to create and simulate various healthcare-related data and scenarios. It aims to assist medical professionals in tasks such as generating medical images, personalized treatment plans, synthetic patient data, and predictive modeling. By harnessing the power of generative models, these apps have the potential to enhance medical research, diagnostics, treatment planning, and education within the healthcare industry.


## Overview
This generative AI healthcare app employs cutting-edge artificial intelligence to produce women healthcare-oriented content and simulations. It leverages generative models to create medical images, patient data, treatment plans, and more, supporting medical practitioners in diagnosis, research, and personalized care. By simulating various healthcare scenarios, these apps contribute to improved medical understanding, training, and decision-making, ushering in a new era of innovation in healthcare.


# Problem Statement :
Fear of the stigma associated with women health services has always been one of the reasons why women avoid making use of such services. This stigma imposes a great deal of mental stress, fear, and depression on patients and causes delays in the diagnosis and treatment of their conditions.

Developing a generative AI health app holds immense potential to revolutionize the way women access and engage with healthcare information. They need to be served with a personalized and comprehensive health companion, providing them with tailored insights into their well-being.

Through this app, we aim at solving the limitations of traditional content creation methods by leveraging advanced AI tehniques ensuring that the generated content by the app aligns with the user’s desired style and context. Our app has the potential to enhance health education, accessibility to information and overall well-being in an empowering manner.


## Objectives :  :notebook_with_decorative_cover:	
- Medical Imaging Generation
- Personalized Treatment Plans
- Medical Text Generation
- Healthcare blogs for suggestions, advice and consultancies
- Virtual Patient Simulation
- ChatBot Integration
- AI Chat Support
- Breast Cancer Detection tool
- Predict Cervical Cancer
- Dedicated Dashboard for Women Health Services
- Menstrual Cycle Tracker
- Predict PCOS using AI in a person on the
basis of their lifestyle and history

# Solution  	:key:
We aim at solving the limitations of traditional content creation methods by leveraging advanced AI tehniques ensuring that the generated content by the app aligns with the user’s desired style and context. Our app has the potential to enhance health education, accessibility to information and overall well-being in an empowering manner.

## Product 
1. HealthCare Generative AI Web App
2. AI Tool Kit For Content Creation
3. Woemn Health App

## Challenges :  	:notebook_with_decorative_cover:
- Balancing AI assistance with content creators' creative input.
- Maintaining content quality and authenticity in automated processes.
- Integrating AI into existing content creation workflows.
- Data privacy and ethical considerations.
- Accurate understanding of user preferences and interests.
- Monitoring and evaluating AI performance.

# Installation of Project

### Prerequisite
PHP v8.1,PHP Mbstring Extension,PHP PDO Extension,PHP FileInfo Extension,PHP JSON Extension,PHP CURL Extension,PHP ZipArchive Extension,PHP symlink() function,PHP shell_exec() function,PHP file_get_contents() function


1. Setup Wamp or Xampp Server in case of Windows or LAMP Server in case of Linux or MAMP in case of Mac, Here I have used Cpanel beacause it is easy to setup in cpanel.

2. Zip and upload only the contents of github Project to the root directory of your hosting server. Ex: /var/www/html/ or /home/username/public_html or whatever is the root folder of your domain/subdomain which will make it reachable as follows: http://yourwebsitename/ like in my case https://learnowx.teamcode.tech
3. After uploading all files and making sure that domain name has proper path set, create Mysql database and proper user in case if you don't already have one, to access this database. You can either create manually via your phpMyAdmin panel or use phpMyAdmin Wizard in your cPanel to create one
4. Now open .env.example file in file manager and add the following details:-
   APP_URL=
   APP_EMAIL=
   DB_CONNECTION=mysql
   DB_HOST=localhost
   DB_PORT=3306
   DB_DATABASE=
   DB_USERNAME=
   DB_PASSWORD=
   OPENAI_SECRET_KEY= Chatgpt api key
  finalyy change .env.example to .env and then save.
5. Finally Import .sql content into phymyadmin of cpanel
6. Boom!! Now you can open your Project with credentials user@gmail.com and admin12345


## Tools and Technology Used:
![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=flat-square&logo=HTML5&logoColor=white)
![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=flat-square&logo=CSS3&logoColor=white)
![Git](https://img.shields.io/badge/Git-F05032?style=flat-square&logo=Git&logoColor=white)
![GitHub](https://img.shields.io/badge/GitHub-181717?style=flat-square&logo=github)
![PHP](https://img.shields.io/badge/-PHP-brightgreen)
![MySQL](https://img.shields.io/badge/-MySQL-yellow)

## AI MODELS 
1. Breast Cancer Detection :
Convolutional Neural Networks to detect the presence of Metastatic tissue and Invasive Ductal Carcinoma Breast’s Histopathological Images.

2. Cervical Cancer Detection :
Voting Ensemble and Extreme Random Trees models predict presence of and preliminary test results according to user’s demographic information, habits, and historic medical records. Using the UCI's Machine Learning repository’s Cervical cancer (Risk Factors) Data Set.

3. PCOS Prediction :
We used a Voting Ensemble model to predict the risk of PCOS on the basis of lifestyle and food intake habits, that are derived from the 2017 Survey Data.
There is a database to store data of patients for user-authentication and connect with the user dashboard to show the lab records and appointments

## Business Model / StartUp Potential  	:moneybag:
Subscription-Based Model: Offer tiered subscription plans for users, providing access to different levels of features such as personalized health assessments, ongoing monitoring, virtual consultations with healthcare professionals, and exclusive educational content.
Freemium with In-App Purchases: Provide a free version of the platform with basic health assessment capabilities, and offer premium add-ons or advanced features for purchase, such as in-depth diagnostic reports, personalized treatment plans, and unlimited access to generative AI health content.
B2B Partnership Model: Collaborate with healthcare providers, clinics, and hospitals to offer a white-label version of the platform as part of their patient care services. Provide data analytics tools and AI-powered insights to healthcare organizations for optimizing patient outcomes and resource allocation.
Data Monetization and Research: Aggregate anonymized and consented user health data to generate valuable insights for pharmaceutical companies, research institutions, and public health agencies. Offer paid access to aggregated, anonymized data sets for research purposes while ensuring strict data privacy standards.

1. Pay-per-Use Model:
Implement a pay-per-use model where users pay a small fee for each content generation request. This approach allows users to pay only for the content they need, making it cost-effective for occasional users or small-scale content generation requirements.

2. API Access and Integration:
Offer an API (Application Programming Interface) for developers and businesses to integrate the content generation tool into their own applications, websites, or platforms. Charge a usage-based fee for API access, allowing developers to incorporate content generation functionality into their products.

3. Data Insights and Analytics:
Provide advanced data insights and analytics as an add-on service. Users can opt to receive detailed reports on content performance, engagement metrics, and audience feedback to refine their content strategies. This data-driven service can be offered as a premium feature or as part of an enhanced subscription tier.

4. Sponsored Content:
Partner with brands or businesses interested in promoting their products or services through generated content. Offer sponsored content slots within the generated output, where advertisers can insert their messages in a non-intrusive manner. This can provide an additional revenue stream while offering relevant content to users.

5. Content Creation Services:
Offer content creation services as an additional service to users who require professional-grade content for marketing campaigns or other purposes. Users can request custom content generated by human writers trained to mimic specific writing styles and tones.

6. In-App Purchases:
Offer in-app purchases for additional features, templates, or content styles that users can use to customize their generated content. These microtransactions can provide users with more options while generating additional revenue for the tool.


### ScreenShots of WeCare
<pre>
<img src="./images/1.jpg" alt="1" width="300" height="600" /> <img src="./images/2.jpg" alt="1" width="300" height="600" /> <img src="./images/3.jpg" alt="1" width="300" height="600" /> <img src="./images/4.jpg" alt="1" width="300" height="600" /> <img src="./images/5.jpg" alt="1" width="300" height="600" /> 
</pre>
### Contributors

- Aman Gupta
- Shashank Kumar
- Richa Agrawal
- Prarthana Agrawal








