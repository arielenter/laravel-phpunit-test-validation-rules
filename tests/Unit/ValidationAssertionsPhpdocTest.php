<?php

namespace Arielenter\ValidationAssertions\Tests\Unit;

use Arielenter\ValidationAssertions\Tests\Support\TransAssertions;
use Arielenter\ValidationAssertions\Tests\TestCase;
use Illuminate\Support\Facades\File;
use PHPUnit\Framework\Attributes\Test;

class ValidationAssertionsPhpdocTest extends TestCase {

    use TransAssertions;

    #[Test]
    public function array_shape_phpdoc_its_stablished_by_its_en_trans(): void {
        $filePath = 'src/Assertions.php';
        $oldCode = File::get($filePath);

        $start = preg_quote('/**', '/');
        $end = 'assertValidationRulesAreImplementedInUrl';
        preg_match("/(\s{4}$start.*?)\n\s{4}public function.*\n\s{4}$end/s",
                $oldCode, $matches);
        $oldPhpdoc = $matches[1];

        $newPhpdoc = $this->tryGetTrans("arielenter_validation_assertions_test"
                . "::readme.array_shape_phpdoc", locale: 'en');

        if ($oldPhpdoc != $newPhpdoc) {
            $newCode = str_replace($oldPhpdoc, $newPhpdoc, $oldCode);
            File::put($filePath, $newCode);
        }

        $this->assertStringContainsString($newPhpdoc, File::get($filePath));
    }
}
