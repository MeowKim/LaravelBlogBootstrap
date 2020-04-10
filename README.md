# Laravel Blog /w Bootstrap

A simple Blog powered by [PHP Laravel Framework](https://laravel.com/) that provides genenral Web & Rest API.  
Check [Postman Documentation](https://documenter.getpostman.com/view/6527807/SzYgQaZe?version=latest) for api details.

## Getting Started

### 1. Clone Repository

```
git clone https://github.com/MeowKim/LaravelBlogBootstrap.git <DIRECTORY>
```

ex) git clone https://github.com/MeowKim/LaravelBlogBootstrap.git blog

### 2. Initialize

```
cd <DIRECTORY>
cp .env.example .env
composer install
```

### 3. Set Permission (optional)

Do this, if actual user is different from current user (ex. working with root credential)

```
chown -R <USER>:<USER_GROUP> <PATH_TO_USER_DIRECTORY>
```

ex) chown -R user:group /home/user

### 4. Edit Environment Configuration (.env)

```
vi .env
```

Following properties would be changed to proper values

-   APP\_\*
-   DB\_\*
-   MAIL\_\*

### 5. DB Migratation

```
php artisan migrate
```
