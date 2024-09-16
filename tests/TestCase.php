<?php

namespace arielenter\ValidationAssertions\Tests;

use arielenter\ValidationAssertions\BaseServiceProvider;
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
    protected function getPackageProviders($app) {
        return [BaseServiceProvider::class];
    }
}
