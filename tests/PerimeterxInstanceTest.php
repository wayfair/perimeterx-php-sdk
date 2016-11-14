<?php

namespace Perimeterx\Tests;

use Perimeterx\Perimeterx;
use PHPUnit\Framework\TestCase;

class PerimeterxInstanceTest extends TestCase
{
    protected function setUp()
    {

    }

    public function testModuleDisabled()
    {
        $px = $this->getInstance(false);
        $this->assertEquals($px->pxVerify(), 1);
    }

    private function getInstance($enabled)
    {
        $pxConfig = [
            'app_id'         => PX_APP_ID,
            'cookie_key'     => PX_COOKIE_KEY,
            'auth_token'     => PX_AUTH_TOKEN,
            'blocking_score' => 60,
            'module_enabled' => $enabled,
        ];
        return Perimeterx::Instance($pxConfig);
    }
}
