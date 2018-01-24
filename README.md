# proxy-middleware-lumen
A proxy/middleware implementation with PHP-Lumen


Composer:

https://getcomposer.org/download/
 
then run 

composer install

set the .env variables for your convenience, there is a .env.example1 ready:<br />
#Name of your company<br />
COMPANY=Company<br />
#Name of your project<br />
PROJECT='Name of your project'<br />
#Name of the header you are getting from Origin to do request<br />
HEADER_NAME_PROXY_URL=X-PROXY-URL<br />
#Timeout for guzzle http requests in seconds<br />
REQ_TIMEOUT=60<br />
<br />
Thats it
