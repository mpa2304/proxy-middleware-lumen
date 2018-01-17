<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Log;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ClientException;
use StdClass;
use DB;
use App\User;


class ProxyController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->setRequestsTimeout();
        $this->setHeaderProxyUrl();
        
    }

    private $headerProxyUrl = null;
    private $requestsTimeout = null;

    public function setRequestsTimeout() {
        $this->requestsTimeout = env('REQ_TIMEOUT') ? (float)env('REQ_TIMEOUT') : 60;
    }

    public function getRequestsTimeout() {
        return $this->requestsTimeout;
    }

    public function setHeaderProxyUrl() {
        $this->headerProxyUrl = env('HEADER_NAME_PROXY_URL') ? env('HEADER_NAME_PROXY_URL') : null;
    }

    public function getHeaderProxyUrl() {
        return $this->headerProxyUrl;
    }

    public function isUrl($url) {

        $isUrl = filter_var($url, FILTER_VALIDATE_URL) ? true : false;

        return $isUrl;
    }

    public function request(Request $request) {

        // https://laravel.com/api/5.5/Illuminate/Http/Request.html

        $h = $this->getHeaderProxyUrl();

        if($h === null) {
            return response()->json(['error' => 'Could not get header name for proxy url'], 503);
        }

        if ($request->hasHeader($h)) {
            $url = $request->header($h);

            if (!$this->isUrl($url)) {
                return response()->json([$h => 'Must be a valid URL'], 422);
            }

            $parsedUrl = parse_url($url); 

        } else {
            return response()->json([$h => 'Required Header'], 422);
        }

        $pRequest = $this->parseRequest($request);

        if (array_key_exists('query', $parsedUrl) || $pRequest->queryString !== null) {
            // convert query string to array to merge with request_params by GET
             
            if (array_key_exists('query', $parsedUrl)) {
                parse_str($parsedUrl['query'], $queryStringProxy);
            } else {
                $queryStringProxy = [];
            }

            if ($pRequest->queryString !== null) {
                parse_str($pRequest->queryString, $queryStringRequest);
            } else {
                $queryStringRequest = [];
            }
        
            $params['query'] = array_merge($queryStringProxy, $queryStringRequest);

        }

        $payload = $this->setPayload($pRequest);

        if (is_object($payload)) {
            $params[$payload->key] = $payload->value;
        }

        $params['timeout'] = $this->getRequestsTimeout();

        $makeRequest = $this->makeRequest($pRequest->method, $url, $params);

        $error = $makeRequest['error'];
        $response = $makeRequest['response'];
        $statusCode = $makeRequest['statusCode'];

        if (!$error) {
            return response($response['body'], $statusCode)
                ->withHeaders([
                    'Content-Type' => $response['contentType']
                ]);            
        }

        if (is_array($error)) {
            return response()->json($error, $makeRequest['statusCode']);
        }

    }

    protected function parseRequest($request) {

        $pRequest = new StdClass();
        $pRequest->method = $request->method();
        $pRequest->uri = $request->path();
        $pRequest->fullUrl = $request->fullUrl();
        $pRequest->inputAll = $request->all();
        $pRequest->input = $request->input();
        $pRequest->queryString = $request->getQueryString();
        $contentType = $request->header('Content-Type');
        $contentType = explode(";", $contentType);
        $pRequest->contentType = $contentType[0];
        $pRequest->post = $request->post();
        $pRequest->body = file_get_contents('php://input');

        return $pRequest;

    }

    protected function setPayload($pRequest) {
        if ($pRequest->method === 'POST' || $pRequest->method === 'PUT' || $pRequest->method === 'PATCH') {
            $payload = new StdClass();
            switch ($pRequest->contentType) {
                case 'application/x-www-form-urlencoded':
                    $payload->key = 'form_params';
                    $payload->value = $pRequest->post;
                     break;
                case 'multipart/form-data':
                    $data = [];
                    foreach ($pRequest->post as $name => $contents) {
                        $data[] = ['name' => $name, 'contents' => $contents];
                    }
                    $payload->key = 'multipart';
                    $payload->value = $data;
                     break;
                case 'application/json':
                    $payload->key = 'json';
                    $payload->value = $pRequest->post;
                    break;
                default:
                    $payload->key = 'body';
                    $payload->value = $pRequest->body;
                    break;
             }

             return $payload;

        } else {

            return false;

        }
    }

    protected function makeRequest($method, $url, $params) {
        $client = new Client();
        $result = [
            'error' => false,
            'statusCode' => null,
            'response' => []
        ];

        try {
            $request = $client->request($method, $url, $params);
        } catch (RequestException $e) {
            $result['error'] = [
                'request' => Psr7\str($e->getRequest()),
                'message' => $e->getMessage()
            ];
          if ($e->hasResponse()) {
            $result['error']['response'] = Psr7\str($e->getResponse());
            $result['statusCode'] = $e->getCode();
            $result['error']['bodyResponse'] = (string)$e->getResponse()->getBody();

          } else {
            $result['statusCode'] = 503;
          }
        }

        if (!$result['error']) {
            $result['response'] = [
                'body' => $request->getBody(), 
                'contentType' => $request->getHeader('Content-Type')
            ];
            $result['statusCode'] = $request->getStatusCode();
        }
        
        return $result;
    }
}


