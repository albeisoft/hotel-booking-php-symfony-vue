Hotel Booking - PHP Symfony Vue (hotel-booking-php-symfony-vue) is a version of example of Symfony and Vue application for Hotel Reservation (Hotel Booking) created on:
- back end (server side) with PHP and Symfony 6 (PHP framework); Symfony technologies used are (and packages): 
	* authentication is with JWT (JSON Web Token)
	* jms/serializer-bundle (JMSSerializerBundle allows you to serialize your data into a requested output format such as JSON, XML, or YAML, and vice versa)
	* friendsofsymfony/rest-bundle (this bundle provides various tools to rapidly develop RESTful API's & applications with Symfony)
	* symfony/maker-bundle (Symfony Maker helps you create empty commands, controllers, form classes, tests and more so you can forget about writing boilerplate code) - so can concentrate more on business logic and not need to "reinvent the wheel"
	* symfony/orm-pack (ORM Pack is a Symfony component, pack for the Doctrine ORM)
	* lexik/jwt-authentication-bundle (this bundle provides JWT (Json Web Token) authentication for your Symfony API)
	* nelmio/NelmioCorsBundle (Adds CORS [Cross-Origin Resource Sharing] headers support in your Symfony application)
	* etc.
- front end (client side, browser side) with JavaScript and: 
	* Vue (Vue.js) version 3.x. that is an JavaScript open-source model–view–viewmodel (MVVM) front end JavaScript framework for building user interfaces and single-page applications (SPA); 
	* Pinia (the intuitive store for Vue.js) - Pinia is a store library for Vue, it allows you to share a state across components/pages. If you are familiar with the Composition API, you might be thinking you can already share a global state with a simple export const state = reactive({}). 
	Pinia builds an easy and properly typed state management system using the new reactivity mechanism and is an excellent library for managing the reactive state of your application. When compared to Vuex, the Pinia API is significantly easier to learn and makes your code a lot easier to read;
	* moment.js (for date formating and date processing with Vue); 
	* PrimeVue (Vue UI Component Library) - PrimeVue is a member of a group of open source UI component libraries provided by PrimeTek. Besides the PrimeVue component library, PrimeTek also provides versions for Java (PrimeFaces), Angular (PrimeNG) and React (PrimeReact);
	* Yup (simple Object schema validation) - Yup is a JavaScript schema builder for validating or parsing values. It allows you to model complex or inter-dependent validations using built-in validators or custom validations using regular expressions;
- database: MariaDB (MariaDB Server is one of the most popular open source relational databases. It's made by the original developers of MySQL and guaranteed to stay open source.). 

Installation

Clone the repository:

git clone https://github.com/albeisoft/hotel-booking-php-symfony-vue.git

Then cd into the folder with this command:

cd hotel-booking-php-symfony-vue/HotelBooking

Then do a composer install:

composer install

Edit .env file with appropriate credential for your database server. Just edit parameter DATABASE_URL for MySQL and updates its configs (make sure you commented out the other DATABASE_URL variables).

Then do a database migration using this command:

1. Create database:	
php bin/console doctrine:database:create

2. Make migration: 
php bin/console make:migration

If migration file exist then execute this command to run the migration the file:
php bin/console doctrine:migrations:migrate

Run server

Run server using this command (you need Symfony CLI [Command Line Interface is a developer tool to help you build, run, and manage your Symfony applications directly from your terminal] to run comands that start with: symfony):

symfony server:start

Or without Symfony CLI:

php bin/console server:start

Then go to http://127.0.0.1:8000 or http://localhost:8000 from your browser and see the web application (back end application).

Run (compile) front end scripts

First install node packages with next command in a terminal from hotel-booking-client-app folder:

npm install

To run Vue application go into folder hotel-booking-client-app and run this command in a Visual Studio (or other IDE: IntelliJ IDEA, etc.) terminal (any change you make to the HTML, CSS, JavaScript, Vue.js code will be reflected immediately in the page you see in your browser).

Run this command (any change you make to the HTML, CSS, JavaScript code will be reflected immediately in the page you see in your browser):

npm run dev

Then go to http://127.0.0.1:5173 or http://localhost:5173 from your browser and see the web application (front end application).
