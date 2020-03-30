# Clone Repository
git clone https://github.com/MeowKim/LaravelBlogBootstrap.git

# Initialize
cd LaravelBlogBootstrap
cp .env.example .env
composer install

# Check DB Connection(.env) & Migrate DB
php artisan migrate

# Set Permission
cd ..
mv LaravelBlogBootstrap <HOME_DIR_NAME>
chown -R <USER_NAME>:<USER_GROUP> <HOME_DIR_NAME>

# Set Web Server(Apache2) Configuration
vi /etc/apache2/sites-available/<SITE_NAME>.conf
...
a2ensite <SITE_NAME>
service apache2 restart