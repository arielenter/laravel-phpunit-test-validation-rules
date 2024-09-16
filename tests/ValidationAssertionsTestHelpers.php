<?php

namespace arielenter\ValidationAssertions\Tests;

use ArgumentCountError;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rules\Password;
use PHPUnit\Framework\AssertionFailedError;
use TypeError;
use function __;
use function validator;

class ValidationAssertionsTestHelpers extends TestCase {

    public string $regexRuleExample = 'regex:'
            . '/^[a-zA-Z]([a-zA-Z0-9]|[a-zA-Z0-9]\.[a-zA-Z0-9])*$/';
    public string $exampleUrl = '/example-url';
    public string $exampleRouteName = 'example_route_name';
    public string $exampleErrorBagName = 'example_error_bag';
    public string $keyPrefix = 'ValidationAssertions::errors';
    public Rule $passowrdRuleInstance;

    protected function setUp(): void {
        parent::setUp();

        $regexRule = $this->regexRuleExample;
        $this->passowrdRuleInstance = Password::min(6);
        $validationRules = [
            'username_field' => ['required', 'string', 'min:6', $regexRule],
            'email_field' => 'email|min:6',
            'password_field' => [$this->passowrdRuleInstance],
            'same_regex_field' => [$regexRule],
        ];

        $callable = fn(Request $request) => $request->validate(
                        $validationRules);

        Route::getRoutes()->add(Route::post($this->exampleUrl, $callable)
                        ->name($this->exampleRouteName));

        Route::getRoutes()->add(Route::delete($this->exampleUrl,
                        fn(Request $request) => $request->validate(
                                ['user_id_field' => 'numeric|max:100'])));

        Route::getRoutes()->add(Route::patch($this->exampleUrl,
                        fn(Request $request) => $request->validateWithBag(
                                $this->exampleErrorBagName,
                                ['username_field' => 'required'])));
    }

    public function checkValidationAssertionThrowsExpectedError(
            string $url,
            string $field,
            mixed $invalidValueExample,
            string|Rule $validationRule,
            string $assertSessionHasErrorsInFail = null
    ): void {

        $transReplace = $this->getTransReplace($url, $field,
                $invalidValueExample, $validationRule,
                $assertSessionHasErrorsInFail);

        $expectedError = __("{$this->keyPrefix}.validation_assertion_"
                . "failed", $transReplace);

        try {
            $this->assertThrows(
                    fn() => $this->assertValidationRuleIsImplementedInUrl(
                            $url, $field, $invalidValueExample,
                            $validationRule),
                    AssertionFailedError::class, $expectedError
            );
        } catch (AssertionFailedError $e) {
            $notImplementedExample = [$url, $field, $invalidValueExample,
                $validationRule,];
            $this->fail('Not implemented rule example '
                    . json_encode($notImplementedExample) . ' did not throw '
                    . 'the expected error. Here is the fail message of '
                    . 'assertThrows: ' . $e->getMessage());
        }
    }

    public function getTransReplace(
            string $url,
            string $field,
            mixed $invalidValueExample,
            string|Rule $validationRule,
            string $assertSessionHasErrorsInFail = null,
            string $requestMethod = 'post',
            string $errorBag = 'default'
    ): array {
        $data = [$field => $invalidValueExample];
        $fieldRule = [$field => $validationRule];
        $validationErrMsg = validator($data, $fieldRule)->messages()->first();

        $assertSessionHasErrorsInFail ??= "Failed asserting that an array "
                . "contains '{$validationErrMsg}'";

        return [
            'url' => $url,
            'method' => $requestMethod,
            'error_bag' => $errorBag,
            'data' => json_encode($data),
            'rule' => json_encode($fieldRule),
            'expected_validation_error' => $validationErrMsg,
            'assert_session_has_errors_in_fail' => $assertSessionHasErrorsInFail
        ];
    }

    public function checkIncorrectRuleValueTypeThrowExpectedError(): void {
        [$a1, $a2, $a3, $a5] = [$this->exampleUrl, 'user_id_field', '101',
            'delete'];
        $composedRule = ['numeric', 'max:100', ['nor string or object']];
        $invalidComposedRuleValue = $composedRule[2];

        $transKey = "{$this->keyPrefix}.incorrect_rule_value_type";
        $replace = [
            'rule' => json_encode($composedRule),
            'value' => json_encode($invalidComposedRuleValue),
            'type' => gettype($invalidComposedRuleValue),
            'correct_types' => 'string|Illuminate\Contracts\Validation\Rule',
        ];

        $this->assertThrows(
                fn() => $this->assertValidationRuleIsImplementedInUrl($a1, $a2,
                        $a3, $composedRule, $a5),
                TypeError::class,
                __($transKey, $replace)
        );
    }

    public function checkIncorrectRuleInstanceThrowExpectedError(): void {
        [$a1, $a2, $a3, $a5] = [$this->exampleUrl, 'user_id_field', '101',
            'delete'];
        $composedRule = ['numeric', 'max:100', (Object) ['incorrect instance']];
        $invalidComposedRuleValue = $composedRule[2];

        $transKey = "{$this->keyPrefix}.incorrect_object_rule";
        $replace = [
            'rule' => get_class($invalidComposedRuleValue),
            'classes' => 'Illuminate\Contracts\Validation\Rule',
        ];

        $this->assertThrows(
                fn() => $this->assertValidationRuleIsImplementedInUrl($a1, $a2,
                        $a3, $composedRule, $a5),
                TypeError::class,
                __($transKey, $replace)
        );
    }

    public function checkValidationAssertionFailsFromTheExpectedListRow(
            array $validationList,
            int $keyOfRowExpectedToFailAssertion
    ): void {
        [$a2, $a3, $a4] = $validationList[$keyOfRowExpectedToFailAssertion];

        $this->assertThrows(
                fn() => $this->assertValidationRulesAreImplementedInUrl(
                        $this->exampleUrl, $validationList),
                AssertionFailedError::class,
                __("{$this->keyPrefix}.validation_assertion_failed",
                        $this->getTransReplace($this->exampleUrl, $a2, $a3,
                                $a4))
        );
    }

    public function invalidRowShapeExamples(): array {
        $i = 1;
        $invalidRowShapeExamples = [];
        $methodPrefix = "invalidRowShapeExample";
        $methodName = "$methodPrefix$i";
        while (method_exists($this, $methodName)) {
            $invalidRowShapeExamples[] = $this->$methodName();
            $i++;
            $methodName = "$methodPrefix$i";
        }
        return $invalidRowShapeExamples;
    }

    public function checkInvalidRowShapeThrowsExpectedError(
            array $invalidRowShapeExample
    ): void {
        [$list, $exceptionClass, $expectedError] = $invalidRowShapeExample;

        try {
            $this->assertThrows(
                    fn() => $this->assertValidationRulesAreImplementedInUrl(
                            $this->exampleUrl, $list),
                    $exceptionClass,
                    $expectedError
            );
        } catch (AssertionFailedError $e) {
            $this->fail('The invalid array shape list example '
                    . json_encode($list) . " did not throw the expected error. "
                    . "Here is the fail message of assertThrows: "
                    . $e->getMessage());
        }
    }

    public function invalidRowShapeExample1(): array {
        $listExample = ['username_field', 'should had been nested', 'required'];

        $transKey = "{$this->keyPrefix}.row_should_had_been_a_nested_"
                . "array";

        $replace = [
            'key' => 0,
            'value' => json_encode($listExample[0]),
            'type' => gettype($listExample[0])
        ];

        return [
            $listExample,
            TypeError::class,
            __($transKey, $replace)
        ];
    }

    public function invalidRowShapeExample2(): array {
        return $this->missingIntKeyRowExample(
                        [
                            'username_field',
                            'key 2 is missing',
                        ], missingIntKey: 2
        );
    }

    public function
    missingIntKeyRowExample(array $exampleRow, int $missingIntKey): array {
        $listExample = [$exampleRow];

        $transKey = "{$this->keyPrefix}.row_has_a_missing_key";

        $replace = $replace = [
            'row_key' => 0,
            'value' => json_encode($exampleRow),
            'missing_key' => $missingIntKey
        ];

        return [
            $listExample,
            ArgumentCountError::class,
            __($transKey, $replace)
        ];
    }

    public function invalidRowShapeExample3(): array {
        return $this->missingIntKeyRowExample(
                        [
                            'username_field',
                            'invalidValueExample' => 'key 2 is still missing',
                            'max:4'
                        ], 2
        );
    }

    public function invalidRowShapeExample4(): array {
        return $this->missingIntKeyRowExample(
                        [
                            'fieldName' => 'username_field',
                            'invalidValueExample' => 'all expected integer '
                            . 'keys are missing, but 0 is validated first',
                            'validationRule' => 'max:4'
                        ], 0
        );
    }

    public function invalidRowShapeExample5(): array {
        return $this->missingIntKeyRowExample(
                        [
                            'username_field',
                            'invalidValueExample' => 'key 1 is missing',
                            2 => 'max:4'
                        ], 1
        );
    }

    public function invalidRowShapeExample6(): array {
        $listExample = [[(object) 'object_field_name?', '', 'required']];
        $row = $listExample[0];
        $fieldName = $listExample[0][0];

        $transKey = "{$this->keyPrefix}.wrong_field_name_value_type";

        $replace = [
            'row_key' => 0,
            'row_value' => json_encode($row),
            'field_key' => 0,
            'field_name' => json_encode($fieldName),
            'actual_type' => gettype($fieldName)
        ];

        return [
            $listExample,
            TypeError::class,
            __($transKey, $replace)
        ];
    }
}
