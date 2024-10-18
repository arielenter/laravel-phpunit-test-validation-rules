<?php

namespace Arielenter\Validation\Exceptions;

use Arielenter\Validation\Constants\TransPrefix;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Support\Facades\Session;
use Orchestra\Testbench\TestCase as OrechestraTestCase;
use PHPUnit\Framework\AssertionFailedError;
use function __;
use function json_encode;

class AssertionFailedException extends AssertionFailedError {

    use TransPrefix;

    public function __construct(
            string $url,
            array $invalidDataExample,
            array $fieldValidationRule,
            string $expectedErrorMessage,
            string $requestMethod,
            string $errorBag,
            array $headers,
            string $errorMsg
    ) {
        $withHeaders = '';
        if (!empty($headers)) {
            $withHeaders = __($this::TRANS_PREFIX . 'with_headers',
                    ['headers' => json_encode($headers)]);
        }
        $message = __(
                $this::TRANS_PREFIX . 'validation_assertion_failed',
                [
                    'url' => $url,
                    'method' => $requestMethod,
                    'error_bag' => $errorBag,
                    'data' => json_encode($invalidDataExample),
                    'rule' => json_encode($fieldValidationRule),
                    'expected_validation_error' => $expectedErrorMessage,
                    'assert_session_has_errors_in_fail' => $errorMsg,
                    'with_headers' => $withHeaders
                ]
        );

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
            string $errorBag,
            array $headers
    ) {
        try {
            $testCase->$requestMethod($url, $invalidDataExample, $headers)
                    ->assertSessionHasErrorsIn($errorBag,
                            [$fieldName => $expectedErrorMessage]);
        } catch (AssertionFailedError $e) {
            throw new self($url, $invalidDataExample, $fieldValidationRule,
                            $expectedErrorMessage, $requestMethod, $errorBag,
                            $headers, $e->getMessage());
        } finally {
            Session::flush();
        }
    }
}
