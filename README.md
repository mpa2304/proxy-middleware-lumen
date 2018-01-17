# proxy-middleware-lumen
A proxy/middleware implementation with PHP-Lumen


Composer:

https://getcomposer.org/download/
 
then run 

composer install

set the .env variables for your convenience, there is a .env.example1 ready:
#Name of your company
COMPANY=Company
#Name of your project
PROJECT=Name of your project
#Name of the header you are getting from Origin to do request
HEADER_NAME_PROXY_URL=X-PROXY-URL
#Timeout for guzzle http requests in seconds
REQ_TIMEOUT=60

Thats it
