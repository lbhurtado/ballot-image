<?php

namespace LBHurtado\BallotImage\Tests;

use Illuminate\Support\Arr;
use LBHurtado\BallotImage\Models\Image;

class ModelTest extends TestCase
{
    /** @test */
    public function model_has_qr_code_and_mac_address_attributes()
    {
        /*** arrange ***/
        $qr_code = $this->faker->word;
        $sender_mac_address = $this->faker->macAddress;

        /*** act ***/
        $image = Image::create($attributes = compact('$qr_code', 'sender_mac_address'));

        /*** assert ***/
        $this->assertEquals($attributes, Arr::only($image->toArray(), array_keys($attributes)));
    }
}
