<?php

namespace Arielenter\ValidationAssertions\Tests;

use Arielenter\Validation\AssertionsTestServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Symfony\Component\Console\Application;

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
