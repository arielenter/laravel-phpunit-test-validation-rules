<?php

namespace Arielenter\Validation\Exceptions;

use Arielenter\Validation\Constants\TransPrefix;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Support\Facades\Session;
use Orchestra\Testbench\TestCase as OrechestraTestCase;
use PHPUnit\Framework\AssertionFailedError;
use function __;

class AssertionFailedException extends AssertionFailedError {

    use TransPrefix;

    public function __construct(array $transReplace) {
        $message = __($this::TRANS_PREFIX . 'validation_assertion_failed', 
                $transReplace);
        
        return parent::__construct($message);
    }

    public static function
    trySubmitInvalidDataExampleToUrlAndAssertItReturnsExpectedErrMsg(
            OrechestraTestCase|TestCase $testCase,
            string $url,
            array $invalidDataExample,
            string $fieldName,
            array $fieldValidationRule,
            string $expectedErrorMessage,
            string $requestMethod,
            string $errorBag
    ) {
        try {
            $testCase->$requestMethod($url, $invalidDataExample)
                    ->assertSessionHasErrorsIn($errorBag,
                            [$fieldName => $expectedErrorMessage]);
        } catch (AssertionFailedError $e) {
            $replace = [
                'url' => $url,
                'method' => $requestMethod,
                'error_bag' => $errorBag,
                'data' => json_encode($invalidDataExample),
                'rule' => json_encode($fieldValidationRule),
                'expected_validation_error' => $expectedErrorMessage,
                'assert_session_has_errors_in_fail' => $e->getMessage()
            ];

            throw new self($replace);
        } finally {
            Session::flush();
        }
    }
}
