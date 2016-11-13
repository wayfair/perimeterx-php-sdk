<?php

namespace Perimeterx\Tests;

use Perimeterx\PerimeterxCookieValidator;
use Perimeterx\Tests\Fixtures\PerimeterxContextGoodCookie;
use Perimeterx\Tests\Fixtures\PerimeterxContextBadCookie;
use Perimeterx\PerimeterxLogger;
use PHPUnit\Framework\TestCase;

class PerimeterxCookieTest extends TestCase
{
    protected function setUp()
    {
    }
    
    public function testGoodCookie() {
        $ctx = new PerimeterxContextGoodCookie();
        $pxCookieValidator = new PerimeterxCookieValidator($ctx, [
            'cookie_key' => PX_COOKIE_KEY,
            'encryption_enabled' => true,
            'blocking_score' => 60,
            'logger' => new PerimeterxLogger()
        ]);

        $verify = $pxCookieValidator->verify();
        $score = $ctx->getScore();
        $this->assertEquals(0,$score);
        $this->assertTrue($verify);
    }

    public function testBadCookie() {
        $ctx = new PerimeterxContextBadCookie();
        $pxCookieValidator = new PerimeterxCookieValidator($ctx, [
            'cookie_key' => PX_COOKIE_KEY,
            'encryption_enabled' => true,
            'blocking_score' => 60,
            'logger' => new PerimeterxLogger()
        ]);

        $verify = $pxCookieValidator->verify();
        $score = $ctx->getScore();
        $this->assertGreaterThan(50,$score);
        $this->assertTrue($verify);
    }
}
