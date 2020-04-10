# Clone Repository

```
git clone https://github.com/MeowKim/LaravelBlogBootstrap.git <DIRECTORY>
```

ex) git clone https://github.com/MeowKim/LaravelBlogBootstrap.git blog

# Initialize

```
cd <DIRECTORY>
cp .env.example .env
composer install
```

# Set Permission, if actual user is different from current user(ex. root user)

```
chown -R <USER>:<USER_GROUP> <PATH_TO_USER_DIRECTORY>
```

ex) chown -R user:group /home/user

# Edit Environment Configuration (.env)

```
vi .env
```

Following properties would be changed to proper values

-   APP\_\*
-   DB\_\*
-   MAIL\_\*

# DB Migratation

```
php artisan migrate
```
