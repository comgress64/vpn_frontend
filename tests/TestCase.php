<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function setUp() {
        parent::setUp();
        \Artisan::call('migrate');
        \DB::beginTransaction();
    }

    public function tearDown() {
        \DB::rollBack();
        parent::tearDown();
    }

}
