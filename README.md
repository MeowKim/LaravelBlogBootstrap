# Laravel Blog /w Bootstrap

## Getting Start

### Clone Repository

```
git clone https://github.com/MeowKim/LaravelBlogBootstrap.git <DIRECTORY>
```

ex) git clone https://github.com/MeowKim/LaravelBlogBootstrap.git blog

### Initialize

```
cd <DIRECTORY>
cp .env.example .env
composer install
```

### Set Permission

(optional) Do this, if actual user is different from current user (ex. working with root credential)

```
chown -R <USER>:<USER_GROUP> <PATH_TO_USER_DIRECTORY>
```

ex) chown -R user:group /home/user

### Edit Environment Configuration (.env)

```
vi .env
```

Following properties would be changed to proper values

-   APP\_\*
-   DB\_\*
-   MAIL\_\*

### DB Migratation

```
php artisan migrate
```

## API

[Postman Documentation](https://documenter.getpostman.com/view/6527807/SzYgQaZe?version=latest)
