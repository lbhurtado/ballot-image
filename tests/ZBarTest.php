<?php

namespace LBHurtado\BallotImage\Tests;

class ZBarTest extends TestCase
{
    /** @test */
    public function config_has_zbar_path()
    {
        /*** arrange ***/
        $path = config('ballot-image.zbar.path');

        /*** act ***/

        /*** assert ***/
        $this->assertNotEmpty($path);
        $this->assertTrue(file_exists($path));
    }
}
