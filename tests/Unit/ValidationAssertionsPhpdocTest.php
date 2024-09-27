<?php

namespace Arielenter\ValidationAssertions\Tests\Unit;

use Arielenter\ValidationAssertions\Tests\Support\TransAssertions;
use Arielenter\ValidationAssertions\Tests\TestCase;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;

class ValidationAssertionsPhpdocTest extends TestCase {

    use TransAssertions;

    #[Test]
    public function assertValidationRulesAreImplementedInUrl_phpdoc(): void {
        $storage = Storage::createLocalDriver(['root' => __DIR__ . '/../../']);
        $filePath = 'src/Assertions.php';
        $oldCode = $storage->get($filePath);

        $start = preg_quote('/**', '/');
        $end = 'assertValidationRulesAreImplementedInUrl';
        preg_match("/(\s{4}$start.*?)\n\s{4}public function.*\n\s{4}$end/s",
                $oldCode, $matches);
        $oldPhpdoc = $matches[1];

        $newPhpdoc = $this->tryGetTrans("arielenter_validation_assertions_test"
                . "::readme.array_shape_phpdoc", locale: 'en');

        if ($oldPhpdoc != $newPhpdoc) {
            $newCode = str_replace($oldPhpdoc, $newPhpdoc, $oldCode);
            $storage->put($filePath, $newCode);
        }

        $this->assertStringContainsString($newPhpdoc, $storage->get(
                        $filePath));
    }
}
