<?php

namespace LBHurtado\BallotImage\Tests;

use Illuminate\Support\Arr;
use LBHurtado\BallotImage\Models\Image;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\File;

class ModelTest extends TestCase
{
    /** @var string */
    protected $sourceImageFile;

    /** @var string */
    protected $destinationImageFile;

    /** $var integer */
    protected $ballot_id = 1;

    public function setUp(): void
    {
        parent::setUp();

        $seed = config('ballot-image.files.image.seed');
        copy($seed, $this->sourceImageFile = config('ballot-image.files.image.source'));
        $this->destinationImageFile = suffixate_filename(config('ballot.files.image.destination'), $this->ballot_id, '-');
    }

    public function tearDown(): void
    {
        if (file_exists($this->sourceImageFile))
            unlink($this->sourceImageFile);

        parent::tearDown();
    }

    /** @test */
    public function model_has_qr_code_and_mac_address_attributes()
    {
        /*** arrange ***/
        $qr_code = $this->faker->word;
        $sender_mac_address = $this->faker->macAddress;

        /*** act ***/
        $image = Image::create($attributes = compact('qr_code', 'sender_mac_address'));

        /*** assert ***/
        $this->assertEquals($attributes, Arr::only($image->toArray(), array_keys($attributes)));
    }

    /** @test */
    public function sender_mac_address_is_a_required_attribute()
    {
        /*** arrange ***/
        $qr_code = $this->faker->word;
        $sender_mac_address = null;

        /*** assert ***/
        $this->expectException(QueryException::class);

        /*** act ***/
        Image::create(compact('qr_code', 'sender_mac_address'));
    }

    /** @test */
    public function qr_code_is_a_nullable_attribute()
    {
        /*** arrange ***/
        $qr_code = null;
        $sender_mac_address = $this->faker->macAddress;

        /*** act ***/
        Image::create($attributes = compact('qr_code', 'sender_mac_address'));

        /*** assert ***/
        $this->assertDatabaseHas((new Image)->getTable(), $attributes);
    }

    /** @test */
    public function image_file_can_be_persisted_in_media_collection()
    {
        /*** arrange ***/
        $image = Image::create(['sender_mac_address' => $this->faker->macAddress]);
        Storage::fake('public');

        /*** act ***/
        $file = UploadedFile::fake()->image($this->sourceImageFile);
        $image->addMedia($file)->toMediaCollection(Image::MEDIA_COLLECTION);

        /*** assert ***/
        Storage::disk('public')->assertExists($image->mediaStorageFileName);
        $this->assertDatabaseHas('images', [
            'id' => $image->media[0]->id,
            'sender_mac_address' => $image->sender_mac_address,
            'qr_code' => null,
        ]);
        list($media_id, $file_name) = explode('/', $image->mediaStorageFileName);
        $this->assertDatabaseHas('media', [
            'id' => $media_id,
            'model_type' => get_class($image),
            'model_id' => $image->id,
            'file_name' => $file_name,
            'collection_name' => Image::MEDIA_COLLECTION
        ]);
    }

    /** @test */
    public function image_can_detect_qr_code()
    {
        /*** arrange ***/
        $image = Image::create(['sender_mac_address' => $this->faker->macAddress]);
        $image->addMedia($this->sourceImageFile)->toMediaCollection(Image::MEDIA_COLLECTION);

        /*** act ***/
        $image->transfuseQRCode();

        /*** assert ***/
        $this->assertEquals('0001-1234', $image->qr_code);
    }
}
