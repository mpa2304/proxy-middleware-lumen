# proxy-middleware-lumen
A proxy/middleware implementation with PHP-Lumen


Composer:

https://getcomposer.org/download/
 
then run 

composer install

set the .env variables for your convenience, there is a .env.example1 ready:\n
#Name of your company\n
COMPANY=Company\n
#Name of your project\n
PROJECT=Name of your project\n
#Name of the header you are getting from Origin to do request\n
HEADER_NAME_PROXY_URL=X-PROXY-URL\n
#Timeout for guzzle http requests in seconds\n
REQ_TIMEOUT=60\n
\n
Thats it
