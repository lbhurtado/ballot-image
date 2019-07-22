<?php

namespace LBHurtado\BallotImage\Tests;

use Illuminate\Support\Facades\File;
use Illuminate\Foundation\Testing\WithFaker;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Spatie\MediaLibrary\MediaLibraryServiceProvider;
use LBHurtado\BallotImage\BallotImageServiceProvider;
use Spatie\SchemalessAttributes\SchemalessAttributesServiceProvider;

class TestCase extends BaseTestCase
{
    use WithFaker;

    public function setUp(): void
    {
        parent::setUp();

        include_once __DIR__.'/../database/migrations/create_images_table.php.stub';
        include_once __DIR__.'/../vendor/spatie/laravel-medialibrary/database/migrations/create_media_table.php.stub';

        (new \CreateImagesTable)->up();
        (new \CreateMediaTable)->up();

        $this->faker = $this->makeFaker('en_PH');

        $path = 'tests/storage/app/public';
        if(!File::isDirectory($path)){
            File::makeDirectory($path, 0777, true, true);
        }
    }

    protected function getPackageProviders($app)
    {
        return [
            BallotImageServiceProvider::class,
            MediaLibraryServiceProvider::class,
            SchemalessAttributesServiceProvider::class,
//            TacticianServiceProvider::class,
//            LaravelTacticianServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
//        $app['config']->set('app.app_storage', 'tests/storage');
//        $app['config']->set('filesystem.default', 'public');
//        $app['config']->set('medialibrary.disk_name', 'public');
        $app['config']->set('ballot-image.zbar.path', '/usr/local/bin/');
        $app['config']->set('ballot-image.files.image.source', 'tests/storage/app/public/ballot-image.jpg');
        $app['config']->set('ballot-image.files.image.destination', 'tests/storage/app/public/ballot-image.jpg');
    }
}
