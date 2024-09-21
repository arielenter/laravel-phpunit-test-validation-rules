<?php

namespace Arielenter\Validation;

use Illuminate\Support\ServiceProvider;

class AssertionsServiceProvider extends ServiceProvider {

    public function boot() {
        $this->registerResources();
    }

    protected function registerResources() {
        $this->loadTranslationsFrom(__DIR__ . '/../lang/production',
                'arielenter_validation_assertions');
    }
}
