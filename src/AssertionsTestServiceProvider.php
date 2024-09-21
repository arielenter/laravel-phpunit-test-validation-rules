<?php

namespace Arielenter\Validation;

class AssertionsTestServiceProvider Extends AssertionsServiceProvider {

    public function boot() {
        parent::boot();

        $this->registerResources();
    }

    protected function registerResources() {
        parent::registerResources();

        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        $this->loadTranslationsFrom(__DIR__ . '/../lang/test',
                'validation_assertions_test');
    }
}
