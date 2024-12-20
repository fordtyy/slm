# Smart Library Management System<img src="./public/images/book_logo.png" width="30"/>

# Getting started

## Installation

Please check the official laravel installation guide for server requirements before you start. [Official Documentation](https://laravel.com/docs/5.4/installation#installation)

Alternative installation is possible without local dependencies relying on [Docker](#docker). 

Clone the repository

    git clone https://github.com/agcodex01/slms.git

Switch to the repo folder

    cd slms

Install all the composer dependencies using composer

    composer install

Install all the node dependencies using npm

    npm install

Compile the application assets using

    npm run dev

Prepare assets for production

    npm run build

Copy the example env file and make the required configuration changes in the .env file

    cp .env.example .env

Generate a new application key

    php artisan key:generate

Create a sqlite database file inside `database` folder name it `database.sqlite`


Run the database migrations (**Set the database connection in .env before migrating**)

    php artisan migrate

Start the local development server

    php artisan serve

You can now access the server at http://localhost:8000

**TL;DR command list**

    git clone https://github.com/agcodex01/slms.git
    cd slms
    composer install
    npm install
    npm run dev
    npm run build
    cp .env.example .env
    php artisan key:generate
    
**Make sure you set the correct database connection information before running the migrations** [Environment variables](#environment-variables)

    php artisan migrate
    php artisan serve

## Database seeding

**Populate the database with seed data with relationships which includes users, articles, comments, tags, favorites and follows. This can help you to quickly start testing the api or couple a frontend and start using it with ready content.**

Open the DummyDataSeeder and set the property values as per your requirement

    database/seeds/DummyDataSeeder.php

Run the database seeder and you're done

    php artisan db:seed

***Note*** : It's recommended to have a clean database before seeding. You can refresh your migrations at any point to clean the database by running the following command

    php artisan migrate:refresh
    

----------

# Code overview

## TALL STACK

- [Tailwind](https://tailwindcss.com) - A utility-first CSS framework
- [Alphine.js](https://alpinejs.dev) - Lightweight, JavaScript framework
- [Laravel](https://laravel.com) - PHP Framework
- [Livewire](https://livewire.laravel.com) - Powerful, dynamic, front-end UIs without leaving PHP.

## Folders

- `app/models` - Contains all the Eloquent models
- `app/filament` - Contains all the Filament Resources
- `app/livewire` - Contains all the Livewire Components
- `bootstrap` - Contains the app.php file which bootstraps the framework.
- `config` - Contains all of your application's configuration files
- `database/factories` - Contains the model factory for all the models
- `database/migrations` - Contains all the database migrations
- `database/seeds` - Contains the database seeder
- `resources` - Contains your views as well as your raw, un-compiled assets such as CSS or JavaScript. 
- `routes` - Contains all the routes file

## Environment variables

- `.env` - Environment variables can be set in this file

***Note*** : You can quickly set the database information and other variables in this file and have the application fully working.

----------
