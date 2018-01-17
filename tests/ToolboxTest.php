<?php

use GuzzleHttp\Client;

class ToolboxTest extends TestCase
{
    /**
     * GET - UserServices/device/{device}
     * https://toolboxdigital.atlassian.net/wiki/spaces/DDP/pages/1114118/Integraci+n+de+API+s+para+Content+Providers#Integraci%C3%B3ndeAPI'sparaContentProviders-APIUserServices
     *
     * @return void
     */
    
    public function testGetMethodApiUserServicesOne()
    {
        $this->get('/UserServices/device/33922e25e42474ad847f8b327a2e5701818d5af5');

        $this->assertResponseStatus(410);

        /*$this->assertEquals(
            $this->app->version(), $this->response->getContent()
        );*/
    }
}
