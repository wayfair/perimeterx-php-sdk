<?php

namespace Perimeterx\Tests;

use Perimeterx\Perimeterx;
use Perimeterx\PerimeterxContext;
use PHPUnit\Framework\TestCase;

class PerimeterxContextTest extends TestCase
{
    protected function setUp()
    {

    }

    public function testHTTPVersion()
    {
        $pxContext = $this->getContext();
        $this->assertEquals($pxContext->getHttpVersion(), '1.1');
    }

    public function testHTTPMethod()
    {
        $pxContext = $this->getContext();
        $this->assertEquals($pxContext->getHttpMethod(), 'GET');
    }

    public function testFullUrl()
    {
        $pxContext = $this->getContext();
        $this->assertEquals($pxContext->getFullUrl(), 'http://localhost/index.php');
    }

    public function testGetUri()
    {
        $pxContext = $this->getContext();
        $this->assertEquals($pxContext->getUri(), '/index.php');
    }

    public function testHostname()
    {
        $pxContext = $this->getContext();
        $this->assertEquals($pxContext->getHostname(), 'localhost');
    }

    public function testHeaders()
    {
        $pxContext = $this->getContext();
        $this->assertEquals($pxContext->getHeaders()['Host'], 'localhost');
    }

    public function testIP()
    {
        $pxContext = $this->getContext();
        $this->assertEquals($pxContext->getIp(), PX_LOCAL_IP_ADDR);
    }

    // public function testPXCookieEmpty()
    // {
    //     $pxContext = $this->getContext();
    //     $this->assertEquals($pxContext->getPxCookie(), null);
    // }

    public function testUserAgent()
    {
        $pxContext = $this->getContext();
        $this->assertEquals($pxContext->getUserAgent(), 'PerimeterX Test');
    }

    // public function testServer2ServerCallReasonEmpty()
    // {
    //     $pxContext = $this->getContext();
    //     $this->assertEquals($pxContext->getS2SCallReason(), null);

    // }

    // public function testScoreEmpty()
    // {
    //     $pxContext = $this->getContext();
    //     $this->assertEquals($pxContext->getScore(), null);
    // }

    // public function testIsMadeS2SRiskApiCall()
    // {
    //     $pxContext = $this->getContext();
    //     $this->assertEquals($pxContext->getIsMadeS2SRiskApiCall(), null);
    // }

    // public function testUUID()
    // {
    //     $pxContext = $this->getContext();
    //     $this->assertEquals($pxContext->getUuid(), null);
    // }

    // public function testVID()
    // {
    //     $pxContext = $this->getContext();
    //     $this->assertEquals($pxContext->getVid(), null);
    // }

    // public function testBlockReason() {
    //     $pxContext = $this->getContext();
    //     $this->assertEquals($pxContext->getBlockReason(), null);
    // }

    public function getContext()
    {
        $pxConfig = [
            'app_id'         => PX_APP_ID,
            'cookie_key'     => PX_COOKIE_KEY,
            'auth_token'     => PX_AUTH_TOKEN,
            'blocking_score' => 0,
        ];
        $_SERVER['HTTP_USER_AGENT'] = 'PerimeterX Test';
        $_SERVER['HTTP_HOST']       = 'localhost';
        $_SERVER['REQUEST_URI']     = '/index.php';
        $_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
        $_SERVER['SERVER_PORT']     = '80';
        $_SERVER['REMOTE_ADDR']     = PX_LOCAL_IP_ADDR;
        $_SERVER['REQUEST_METHOD']  = 'GET';
        return new PerimeterxContext($pxConfig);
    }
}
