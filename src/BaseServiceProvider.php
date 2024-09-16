<?php

namespace arielenter\ValidationAssertions;

use Illuminate\Support\ServiceProvider;

class BaseServiceProvider extends ServiceProvider {

    public function boot() {
        $this->registerResources();
    }
    
    private function registerResources() {
        $this->loadTranslationsFrom(__DIR__ . '/../lang', 'ValidationAssertions');
    }
}
