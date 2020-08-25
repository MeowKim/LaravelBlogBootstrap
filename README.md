# Laravel Blog /w Bootstrap

## :pencil: About
A simple blog that provides genenral Web & Rest API.  
Powered by [PHP Laravel Framework](https://laravel.com/) & [Bootstrap CSS Toolkit](https://getbootstrap.com/) (Front-end scaffolding).  
Check [Postman Documentation](https://documenter.getpostman.com/view/6527807/SzYgQaZe?version=latest) for API details.  

Some Markdown text with <span style="color:blue">some *blue* text</span>.

## :car: Getting Started

### Clone
Clone this repo to your local machine.
```
$ git clone https://github.com/MeowKim/LaravelBlogBootstrap.git
```

### Install PHP packages
Use **composer** to install
```
$ composer install
```

### Install JS packages & compile assets
Use **yarn** to install.
```
$ yarn
$ yarn dev
```
or use **npm**.
```
$ npm install
$ npm run dev
```

### Set Environment
Create `.env` file from existing example and fill required items.
```
$ cp .env.example .env
$ vi .env
```
> Items about DB connection (DB_*) MUST be filled with your own.
> Others are optional.

### Generate key
Create key for your laravel app via artisan command.
```
$ php artisan key:generate
```

### Migration
Migrate DB with given seed files.  
```
$ php artisan migrate
```

## :unlock: License
Copyright &copy;2020 [MeowKim](https://github.com/MeowKim)  
Distributed under the [MIT](https://github.com/MeowKim/LaravelBlogBootstrap/blob/master/LICENSE) License.  
