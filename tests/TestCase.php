<?php

namespace Arielenter\ValidationAssertions\Tests;

use Arielenter\Validation\AssertionsTestServiceProvider;
use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase {

    /**
     * Get package providers.
     *
     * @api
     *
     * @param  Application  $app
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array {
        return [AssertionsTestServiceProvider::class];
    }
}
