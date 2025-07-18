<?php

namespace Arielenter\Validation\Exceptions;

use Arielenter\Validation\Constants\AssertionsTrans;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use Orchestra\Testbench\TestCase as OrechestraTestCase;
use PHPUnit\Framework\AssertionFailedError;
use function __;
use function json_encode;

class Assertion extends AssertionFailedError {

    use AssertionsTrans;

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
            $withHeaders = __($this::ASSERTIONS_ERRORS_TRANS . 'with_headers',
                    ['headers' => json_encode($headers)]);
        }
        $message = __(
                $this::ASSERTIONS_ERRORS_TRANS . 'validation_assertion_failed',
                [
                    'url' => $url,
                    'method' => $requestMethod,
                    'error_bag' => $errorBag,
                    'data' => json_encode($invalidDataExample),
                    'rule' => json_encode($fieldValidationRule),
                    'expected_validation_error' => $expectedErrorMessage,
                    'assert_invalid_fail_msg' => $errorMsg,
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
            array $headers,
            int $options
    ): void {
        try {
            if (in_array($requestMethod, ['get', 'getJson'])) {
                $response = self::getMethod($testCase, $requestMethod, $url,
                                $invalidDataExample, $headers, $options);
            } else {
                $response = self::allOtherMethods($testCase, $requestMethod,
                                $url, $invalidDataExample, $headers, $options);
            }
            $response->assertInvalid([$fieldName => $expectedErrorMessage],
                    $errorBag);
        } catch (AssertionFailedError $e) {
            throw new self($url, $invalidDataExample, $fieldValidationRule,
                            $expectedErrorMessage, $requestMethod, $errorBag,
                            $headers, $e->getMessage());
        } finally {
            Session::forget('errors');
        }
    }

    private static function getMethod(
            OrechestraTestCase|TestCase $testCase,
            string $method,
            string $url,
            array $invalidDataExample,
            array $headers,
            int $options
    ): TestResponse {
        $urlWithParam = URL::query($url, $invalidDataExample);
        if (Str::endsWith($method, 'Json')) {
            return $testCase->$method($urlWithParam, $headers, $options);
        }
        return $testCase->$method($urlWithParam, $headers);
    }

    private static function allOtherMethods(
            OrechestraTestCase|TestCase $testCase,
            string $method,
            string $url,
            array $invalidDataExample,
            array $headers,
            int $options
    ): TestResponse {
        if (Str::endsWith($method, 'Json')) {
            return $testCase->$method($url, $invalidDataExample, $headers,
                            $options);
        }
        return $testCase->$method($url, $invalidDataExample, $headers);
    }
}
