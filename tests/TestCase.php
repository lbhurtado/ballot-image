<?php

namespace LBHurtado\BallotImage\Tests;

use Illuminate\Support\Facades\File;
use Illuminate\Foundation\Testing\WithFaker;
use Orchestra\Testbench\TestCase as BaseTestCase;
use LBHurtado\BallotImage\BallotImageServiceProvider;
use Spatie\SchemalessAttributes\SchemalessAttributesServiceProvider;

class TestCase extends BaseTestCase
{
    use WithFaker;

    public function setUp(): void
    {
        parent::setUp();

        include_once __DIR__.'/../database/migrations/create_images_table.php.stub';
        (new \CreateImagesTable)->up();

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
        $app['config']->set('ballot.zbar.path', '/usr/local/bin/');
    }
}
