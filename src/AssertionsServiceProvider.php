<?php

namespace arielenter\Validation;

use Illuminate\Support\ServiceProvider;

class AssertionsServiceProvider extends ServiceProvider {

    public function boot() {
        $this->registerResources();
    }
    
    private function registerResources() {
        $this->loadTranslationsFrom(__DIR__ . '/../lang', 'ValidationAssertions');
    }
}
