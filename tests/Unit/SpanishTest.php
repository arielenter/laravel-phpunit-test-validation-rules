<?php

namespace Arielenter\ValidationAssertions\Tests\Unit;

use Illuminate\Support\Facades\App;

class SpanishTest extends ValidationAssertionsTest {
    public function afterApplicationCreated($callback): void {
        App::setLocale('es');
        parent::afterApplicationCreated($callback);
    }
}
