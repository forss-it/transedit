<?php

namespace Dialect\TransEdit;

use Illuminate\Foundation\Testing\RefreshDatabase;

class TestCase extends \Orchestra\Testbench\TestCase
{
    use RefreshDatabase;

    protected function setUp()
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [TransEditServiceProvider::class];
    }
}
