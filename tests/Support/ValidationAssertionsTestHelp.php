<?php

namespace Arielenter\ValidationAssertions\Tests\Support;

use Arielenter\Validation\Constants\AssertionsTrans;
use Arielenter\Validation\Exceptions\FieldNameValueType;
use Arielenter\Validation\Exceptions\ObjectRule;
use Arielenter\Validation\Exceptions\RowHasRequiredKeys;
use Arielenter\Validation\Exceptions\RowValueType;
use Arielenter\Validation\Exceptions\RuleValueType;
use Arielenter\ValidationAssertions\Tests\Support\TransAssertions;
use Exception;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rules\Password;
use PHPUnit\Framework\AssertionFailedError;
use TypeError;
use function __;
use function json_encode;
use function validator;

trait ValidationAssertionsTestHelp {

    use TransAssertions,
        AssertionsTrans;

    public string $regexRule = 'regex:/^[a-z]([a-z0-9]|[a-z0-9]\.[a-z0-9])*$/i';
    public string $exampleUrl = '/example-url';
    public string $exampleRouteName = 'example_route_name';
    public string $exampleErrorBagName = 'example_error_bag';
    public Rule $passowrdRuleInstance;
    public array $exampleValidationRules;

    public function setUp(): void {
        parent::setUp();

        $this->passowrdRuleInstance = Password::min(6);

        Route::getRoutes()->add(Route::patch($this->exampleUrl,
                        fn(Request $request) => $request->validateWithBag(
                                $this->exampleErrorBagName,
                                ['username_field' => 'required'])));

        Route::getRoutes()->add(
                Route::post(
                        $this->exampleUrl,
                        fn(Request $request) => $request->validate(
                                $this->getExampleValidationRules())
                )->name($this->exampleRouteName)
        );

        Route::getRoutes()->add(Route::get($this->exampleUrl,
                        fn(Request $request) => $request->validate(
                                ['user_id_field' => 'numeric'])));

        Route::getRoutes()->add(Route::delete($this->exampleUrl,
                        fn(Request $request) => $request->validate(
                                ['user_id_field' => 'numeric|max:100'])));

        Route::getRoutes()->add(Route::put($this->exampleUrl,
                        fn(Request $request) => $this->putCallable($request)));

        Route::getRoutes()->add(Route::options($this->exampleUrl,
                        fn(Request $request) => $this->optionsCall($request)));
    }

    public function getExampleValidationRules(): array {
        $regexRule = $this->regexRule;

        return [
            'username_field' => ['required', 'string', 'min:6', $regexRule],
            'email_field' => 'email|min:6',
            'password_field' => [$this->passowrdRuleInstance],
            'same_regex_field' => [$regexRule],
        ];
    }

    public function optionsCall(Request $request): void {
        if ($request->wantsJson()) {
            $expectedContent = json_encode(
                    ['json_field' => $request->get('json_field')],
                    JSON_HEX_TAG
            );
            $actualContent = $request->getContent();
            if ($actualContent == $expectedContent) {
                $request->validate(['json_field' => 'numeric']);
            }

            $errorMsg = __('Request content doesn’t match what was expected. '
                    . 'Expected is ‘:expected’ but ‘:actual’ was received '
                    . 'instead.',
                    ['expected' => $expectedContent,
                        'actual' => $actualContent]);
            throw new Exception($errorMsg);
        }
        throw new Exception('A Json Phpunit’s request method was expected to '
                        . 'be used.');
    }

    public function putCallable(Request $request): void {
        $headers = $request->headers->all();
        if (array_key_exists('example', $headers)) {
            $request->validate(['field' => 'required']);
        }

        throw new Exception('Header ‘example’ was not present on the '
                        . 'request.');
    }

    public function checkValidationAssertionThrowsExpectedError(
            string $url,
            string $field,
            mixed $invalidValueExample,
            string|Rule $validationRule,
            ?string $assertInvalidFailMsg = null,
            string $requestMethod = 'post',
            array $headers = [],
            string $errorBag = 'default'
    ): void {
        $transReplace = $this->getTransReplace($url, $field,
                $invalidValueExample, $validationRule,
                $assertInvalidFailMsg, $requestMethod, $headers,
                $errorBag);

        $expectedError = $this->tryGetTrans($this::ASSERTIONS_ERRORS_TRANS
                . "validation_assertion_failed", $transReplace);

        try {
            $this->assertThrows(
                    fn() => $this->assertValidationRuleIsImplementedInUrl(
                            $url, $field, $invalidValueExample,
                            $validationRule, $requestMethod, $errorBag,
                            $headers),
                    AssertionFailedError::class, $expectedError
            );
        } catch (AssertionFailedError $e) {
            $notImplementedRuleExample = [$url, $field, $invalidValueExample,
                $validationRule,];

            $this->throwNotExpectedErrorReceived($notImplementedRuleExample,
                    $e->getMessage());
        }
    }

    public function getTransReplace(
            string $url,
            string $field,
            mixed $invalidValueExample,
            string|Rule $validationRule,
            ?string $assertInvalidFailMsg = null,
            string $requestMethod = 'post',
            array $headers = [],
            string $errorBag = 'default',
    ): array {
        $data = [$field => $invalidValueExample];
        $fieldRule = [$field => $validationRule];
        $validationErrMsg = validator($data, $fieldRule)->messages()->first();

        $assertInvalidFailMsg ??= "Failed to find a validation error for key "
                . "and message: '{$field}' => '{$validationErrMsg}'";

        return [
            'url' => $url,
            'method' => $requestMethod,
            'error_bag' => $errorBag,
            'data' => json_encode($data),
            'rule' => json_encode($fieldRule),
            'expected_validation_error' => $validationErrMsg,
            'assert_invalid_fail_msg' =>
            $assertInvalidFailMsg,
            'with_headers' => $this->getWithHeaders($headers)
        ];
    }

    public function getWithHeaders(array $headers) {
        if (empty($headers)) {
            return '';
        }
        return $this->tryGetTrans($this::ASSERTIONS_ERRORS_TRANS
                        . 'with_headers', ['headers' => json_encode($headers)]);
    }

    public function throwNotExpectedErrorReceived(
            array $notImplementedRuleExample,
            string $assertThrowsError
    ): void {
        $errorMsg = $this->getNotExpectedErrorReceivedMsg();
        $replace = [
            'example_type' => "'not implemented' rule",
            'example' => json_encode($notImplementedRuleExample),
            'assertion_error' => $assertThrowsError
        ];

        $this->fail(__($errorMsg, $replace));
    }

    public function getNotExpectedErrorReceivedMsg(): string {
        return 'The following :example_type example :example did not throw the '
                . 'expected error. Here is the fail message of assertThrows: '
                . ':assertion_error';
    }

    public function checkIncorrectRuleValueTypeThrowExpectedError(): void {
        [$a1, $a2, $a3, $a5] = [$this->exampleUrl, 'user_id_field', '101',
            'delete'];
        $composedRule = ['numeric', 'max:100', ['nor string or object']];
        $invalidComposedRuleValue = $composedRule[2];

        $this->assertThrows(
                fn() => $this->assertValidationRuleIsImplementedInUrl($a1, $a2,
                        $a3, $composedRule, $a5),
                TypeError::class,
                $this->getIncorrectRuleValueTypeError(
                        $composedRule,
                        $invalidComposedRuleValue,
                        'string|' . Rule::class
                )
        );
    }

    public function getIncorrectRuleValueTypeError(
            mixed $rule,
            mixed $invalidValueFromRule,
            string $correctTypes
    ) {
        $transKey = $this::ASSERTIONS_ERRORS_TRANS
                . "incorrect_rule_value_type";
        $replace = [
            'rule' => json_encode($rule),
            'value' => json_encode($invalidValueFromRule),
            'type' => gettype($invalidValueFromRule),
            'correct_types' => $correctTypes,
        ];

        return $this->tryGetTrans($transKey, $replace);
    }

    public function checkIncorrectRuleInstanceThrowExpectedError(): void {
        [$a1, $a2, $a3, $a5] = [$this->exampleUrl, 'user_id_field', '101',
            'delete'];
        $composedRule = [
            'numeric', 'max:100', (Object) ['not instanceof supported classes']
        ];
        $invalidComposedRuleValue = $composedRule[2];

        $this->assertThrows(
                fn() => $this->assertValidationRuleIsImplementedInUrl($a1, $a2,
                        $a3, $composedRule, $a5),
                TypeError::class,
                $this->getIncorrectRuleInstanceError($invalidComposedRuleValue)
        );
    }

    public function getIncorrectRuleInstanceError(
            object $invalidComposedRuleValue
    ) {
        $transKey = $this::ASSERTIONS_ERRORS_TRANS . "incorrect_object_rule";
        $replace = [
            'rule' => get_class($invalidComposedRuleValue),
            'classes' => Rule::class
        ];

        return $this->tryGetTrans($transKey, $replace);
    }

    public function checkValidationAssertionFailsFromTheExpectedListRow(
            array $validationList,
            int $keyOfRowExpectedToFailAssertion,
            ?string $assertInvalidFailMsg = null
    ): void {
        [$a2, $a3, $a4] = $validationList[$keyOfRowExpectedToFailAssertion];

        $replace = $this->getTransReplace($this->exampleUrl, $a2, $a3, $a4,
                $assertInvalidFailMsg);

        $expectedErrorMsg = $this->tryGetTrans($this::ASSERTIONS_ERRORS_TRANS
                . "validation_assertion_failed", $replace);

        $this->assertThrows(
                fn() => $this->assertValidationRulesAreImplementedInUrl(
                        $this->exampleUrl, $validationList),
                AssertionFailedError::class,
                $expectedErrorMsg
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
            $errorMsg = $this->getNotExpectedErrorReceivedMsg();
            $replace = [
                'example_type' => "'invalid array shape' list",
                'example' => json_encode($list),
                'assertion_error' => $e->getMessage()
            ];
            $this->fail(__($errorMsg, $replace));
        }
    }

    public function invalidRowShapeExample1(): array {
        $listExample = ['this row', 'should had been', 'nested'];

        $transKey = $this::ASSERTIONS_ERRORS_TRANS . "row_should_had_been_a_"
                . "nested_array";

        $replace = [
            'key' => 0,
            'value' => json_encode($listExample[0]),
            'type' => gettype($listExample[0])
        ];

        return [
            $listExample,
            RowValueType::class,
            $this->tryGetTrans($transKey, $replace)
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

        $transKey = $this::ASSERTIONS_ERRORS_TRANS . "row_has_a_missing_key";

        $replace = $replace = [
            'row_key' => 0,
            'value' => json_encode($exampleRow),
            'missing_key' => $missingIntKey
        ];

        return [
            $listExample,
            RowHasRequiredKeys::class,
            $this->tryGetTrans($transKey, $replace)
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
        $listExample = [[(object) 'an_object_field_name?', '', 'required']];
        $row = $listExample[0];
        $fieldName = $listExample[0][0];

        $transKey = $this::ASSERTIONS_ERRORS_TRANS . "wrong_field_name_value_"
                . "type";

        $replace = [
            'row_key' => 0,
            'row_value' => json_encode($row),
            'field_key' => 0,
            'field_name' => json_encode($fieldName),
            'actual_type' => gettype($fieldName)
        ];

        return [
            $listExample,
            FieldNameValueType::class,
            $this->tryGetTrans($transKey, $replace)
        ];
    }

    public function invalidRowShapeExample7(): array {
        $listExample = [['username_field', '', true]];
        $invalidRuleValue = $listExample[0][2];

        return [
            $listExample,
            RuleValueType::class,
            $this->getIncorrectRuleValueTypeError(
                    $invalidRuleValue,
                    $invalidRuleValue,
                    'string|array|' . Rule::class
            )
        ];
    }

    public function invalidRowShapeExample8(): array {
        $listExample = [
            ['username_field', '', (object) ['not intanceof supported classes']]
        ];
        $invalidRuleValue = $listExample[0][2];

        return [
            $listExample,
            ObjectRule::class,
            $this->getIncorrectRuleInstanceError($invalidRuleValue)
        ];
    }
}
