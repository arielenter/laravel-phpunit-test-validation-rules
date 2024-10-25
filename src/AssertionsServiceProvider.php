<?php

namespace Arielenter\Validation;

use Arielenter\Validation\Constants\AssertionsTrans;
use Illuminate\Support\ServiceProvider;

class AssertionsServiceProvider extends ServiceProvider {
    use AssertionsTrans;

    public function boot() {
        $this->registerResources();
    }

    protected function registerResources() {
        $this->loadTranslationsFrom(__DIR__ . '/../lang/production',
                $this::ASSERTIONS_TRANS);
    }
}
