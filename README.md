# proxy-middleware-lumen
A proxy/middleware implementation with PHP-Lumen


Composer:


https://getcomposer.org/download/


cd /path/to/project

composer install


touch .env


set the .env variables for your convenience, there is a .env.example1 ready:<br />
#This is the token to use the auth middleware, you can insert several token in db with the seeder
DEFAULT_API_TOKEN=PUT-HERE-YOUR-DEFAULT-API-TOKEN
#Name of your company<br />
COMPANY=Company<br />
#Name of your project<br />
PROJECT='Name of your project'<br />
#Name of the header you are getting from Origin to do request<br />
HEADER_NAME_PROXY_URL=X-PROXY-URL<br />
#Timeout for guzzle http requests in seconds<br />
REQ_TIMEOUT=60<br />
<br />
In case you use sqlite type: 

touch /database/database.sqlite 


Create users table: 

php artisan migrate


Create the first user with the .env default api token:

php artisan db:seed —class=UsersTableSeeder

To test locally: 

php -S localhost:8000 -t public
