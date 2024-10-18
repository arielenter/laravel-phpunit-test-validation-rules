<?php

namespace Arielenter\ValidationAssertions\Tests\Unit;

use Arielenter\Validation\Assertions as ValidationAssertions;
use Arielenter\Validation\Constants\SupportedRequestMethods;
use Arielenter\Validation\Exceptions\NotAnInvalidValueExampleForGivenRuleException;
use Arielenter\Validation\Exceptions\UnknownRuleGivenException;
use Arielenter\Validation\Exceptions\UnsupportedRequestMethodException;
use Arielenter\ValidationAssertions\Tests\Support\TransAssertions;
use Arielenter\ValidationAssertions\Tests\Support\ValidationAssertionsTestHelpers;
use Arielenter\ValidationAssertions\Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Session;
use PHPUnit\Framework\Attributes\Test;

class ValidationAssertionsTest extends TestCase {

    use ValidationAssertions,
        TransAssertions,
        ValidationAssertionsTestHelpers,
        SupportedRequestMethods;

    public string $missingErrorKeyMsg = "Session is missing expected key "
            . "[errors].\nFailed asserting that false is true.";

    #[Test]
    public function will_pass_if_validation_is_implemented_in_url(): void {
        $this->assertValidationRuleIsImplementedInUrl(
                url: $this->exampleUrl,
                fieldName: 'username_field',
                invalidValueExample: '',
                validationRule: 'required',
                requestMethod: 'patch',
                errorBag: $this->exampleErrorBagName
        );
    }

    #[Test]
    public function a_route_name_can_be_given_instead_of_a_url(): void {
        $this->assertValidationRuleIsImplementedInRouteName(
                routeName: $this->exampleRouteName,
                fieldName: 'username_field',
                invalidValueExample: '',
                validationRule: 'required',
                requestMethod: 'patch',
                errorBag: $this->exampleErrorBagName
        );
    }

    #[Test]
    public function if_no_error_bag_is_given_default_is_used(): void {
        $this->assertValidationRuleIsImplementedInUrl($this->exampleUrl,
                'user_id_field', 'not a number', 'numeric', 'delete');
    }

    #[Test]
    public function if_no_request_method_is_specified_post_is_assumed(): void {
        $this->assertValidationRuleIsImplementedInUrl($this->exampleUrl,
                'username_field', '', 'required');
    }

    #[Test]
    public function session_is_flush_after_every_assertion(): void {
        $this->will_pass_if_validation_is_implemented_in_url();

        $this->assertEmpty(Session::all());
    }

    #[Test]
    public function will_fail_if_validation_is_not_implemented_in_url(): void {
        $this->checkValidationAssertionThrowsExpectedError($this->exampleUrl,
                'username_field', 'confirmed is not implemented for username',
                'confirmed');

        $this->checkValidationAssertionThrowsExpectedError($this->exampleUrl,
                'non_implemented_field', '', 'required');

        $this->checkValidationAssertionThrowsExpectedError('/nonexistent-url',
                'username_field', '', 'required', $this->missingErrorKeyMsg);
    }

    #[Test]
    public function instances_of_validation_rule_can_be_used(): void {
        $this->assertValidationRuleIsImplementedInUrl($this->exampleUrl,
                'password_field', 'short', $this->passowrdRuleInstance);
    }

    #[Test]
    public function files_can_be_used_as_an_invalid_value(): void {
        $this->assertValidationRuleIsImplementedInUrl($this->exampleUrl,
                'username_field', UploadedFile::fake()->image('avatar.jpg'),
                'string');
    }

    #[Test]
    public function a_composed_validation_rule_can_be_used(): void {
        [$a1, $a2, $a3, $a5] = [$this->exampleUrl, 'user_id_field', '101',
            'delete'];

        $this->assertValidationRuleIsImplementedInUrl($a1, $a2, $a3,
                'numeric|max:100', $a5);

        $this->assertValidationRuleIsImplementedInUrl($a1, $a2, $a3,
                ['numeric', 'max:100'], $a5);
    }

    #[Test]
    public function
    composed_validation_rule_value_types_will_be_validated(): void {
        $this->checkIncorrectRuleValueTypeThrowExpectedError();

        $this->checkIncorrectRuleInstanceThrowExpectedError();
    }

    #[Test]
    public function
    error_is_thrown_if_a_not_invalid_value_example_is_given(): void {
        [$a1, $a2, $a3, $a4] = [$this->exampleUrl, 'username_field',
            'not empty', 'required'];

        $replace = [
            'data' => json_encode([$a2 => $a3]),
            'rule' => json_encode([$a2 => $a4])
        ];

        $this->assertThrows(
                fn() => $this->assertValidationRuleIsImplementedInUrl($a1,
                        $a2, $a3, $a4),
                NotAnInvalidValueExampleForGivenRuleException::class,
                $this->tryGetTrans($this::TRANS_PREFIX . "not_invalid_data",
                        $replace)
        );
    }

    #[Test]
    public function only_supported_request_methods_are_permited(): void {
        [$a1, $a2, $a3, $a4, $a5] = [$this->exampleUrl, 'user_id_field',
            'not a number', 'numeric', 'unsupported method'];

        $replace = [
            'method' => $a5,
            'supported_methods' => json_encode($this::SUPPORTED_METHODS)
        ];

        $this->assertThrows(
                fn() => $this->assertValidationRuleIsImplementedInUrl($a1,
                        $a2, $a3, $a4, $a5),
                UnsupportedRequestMethodException::class,
                $this->tryGetTrans($this::TRANS_PREFIX . "unsupported_request_"
                        . "method", $replace)
        );
    }

    #[Test]
    public function rule_must_exist(): void {
        [$a1, $a2, $a3, $a4] = [$this->exampleUrl, 'username_field',
            'invalid value example', 'non existen or misspelled rule'];

        $replace = [
            'rule' => json_encode($a4),
            'validator_error' => 'Method Illuminate\Validation\Validator::'
            . 'validateNonExistenOrMisspelledRule does not exist.'
        ];

        $this->assertThrows(
                fn() => $this->assertValidationRuleIsImplementedInUrl($a1,
                        $a2, $a3, $a4),
                UnknownRuleGivenException::class,
                $this->tryGetTrans($this::TRANS_PREFIX . "unknown_rule_given",
                        $replace)
        );
    }

    #[Test]
    public function multiple_validation_rules_can_be_tested_at_once(): void {
        $this->assertValidationRulesAreImplementedInUrl(
                url: $this->exampleUrl,
                list: [
                    ['user_id_field', 'not a number', 'numeric'],
                    ['user_id_field', '101', 'numeric|max:100']
                ],
                requestMethod: 'delete'
        );
    }

    #[Test]
    public function a_route_name_can_also_be_given(): void {
        $this->assertValidationRulesAreImplementedInRouteName(
                routeName: $this->exampleRouteName,
                list: [
                    ['user_id_field', 'not a number', 'numeric'],
                    ['user_id_field', '101', 'numeric|max:100']
                ],
                requestMethod: 'delete'
        );
    }

    #[Test]
    public function
    will_fail_if_one_of_the_given_validation_rules_is_not_implemented(): void {
        $list1 = [
            ['username_field', '', 'required'],
            [
                'username_field',
                'confirmed is not implemented for username_field',
                'confirmed'
            ],
            ['email_field', 'not an email', 'email']
        ];

        $this->checkValidationAssertionFailsFromTheExpectedListRow($list1,
                keyOfRowExpectedToFailAssertion: 1);

        $list2 = [
            ['username_field', '', 'required'],
            ['email_field', 'not an email', 'email'],
            ['non_implemented_field', '', 'required'],
        ];

        $this->checkValidationAssertionFailsFromTheExpectedListRow($list2, 2);
    }

    #[Test]
    public function
    multiple_fields_that_share_the_same_validation_rule_can_be_grouped(): void {
        $this->assertValidationRulesAreImplementedInUrl(
                $this->exampleUrl,
                [[['username_field', 'email_field'], 'short', 'min:6']]
        );
    }

    #[Test]
    public function
    multiple_invalid_value_examples_can_be_associated_to_a_field(): void {
        /**
         * The following regex rule example must be nested inside an array 
         * because it contains a pipe | in it, which are used in delimited 
         * strings composed rules (example 'numeric|max:100'). For this reason 
         * if the regex example were not to be encapsulated into an array, the 
         * validator confuses it as two separated rules which won't 
         * work.
         * 
         */
        $this->assertValidationRulesAreImplementedInUrl(
                $this->exampleUrl,
                [
                    [
                        'username_field',
                        ['0invalid', 'inva..lid', 'invalid.', 'inv@lid'],
                        [$this->regexRule]
                    ],
                ]
        );
    }

    #[Test]
    public function
    multiple_fields_can_have_the_same_set_of_multiple_invalid_value_examples():
    void {
        $this->assertValidationRulesAreImplementedInUrl(
                $this->exampleUrl,
                [
                    [
                        ['username_field', 'same_regex_field'],
                        ['0invalid', 'inva..lid', 'invalid.', 'inv@lid'],
                        [$this->regexRule]
                    ]
                ]
        );
    }

    #[Test]
    public function list_rows_must_have_the_correct_array_shape(): void {
        foreach (
                $this->invalidRowShapeExamples() as $invalidRowShapeExample
        ) {
            $this->checkInvalidRowShapeThrowsExpectedError(
                    $invalidRowShapeExample);
        }
    }

    #[Test]
    public function headers_can_also_be_send_as_part_of_the_assertions(): void {
        $exampleUrl = $this->exampleUrl;
        $exampleRoute = $this->exampleRouteName;
        [$field, $invalidVal, $rule, $method] = ['field', '', 'required',
            'put', ['example' => 'header']];
        $exampleHeaders = ['example' => 'header'];

        $this->assertValidationRuleIsImplementedInUrl($exampleUrl, $field,
                $invalidVal, $rule, $method, headers: $exampleHeaders);

        $this->assertValidationRuleIsImplementedInRouteName($exampleRoute,
                $field, '', 'required', $method, headers: $exampleHeaders);

        $list = [[$field, $invalidVal, $rule]];

        $this->assertValidationRulesAreImplementedInUrl($exampleUrl, $list,
                $method, headers: $exampleHeaders);

        $this->assertValidationRulesAreImplementedInRouteName($exampleRoute,
                $list, $method, headers: $exampleHeaders);

        $this->checkValidationAssertionThrowsExpectedError($exampleUrl, $field,
                'not a number', 'numeric', $this->missingErrorKeyMsg, $method,
                $exampleHeaders);
    }
}
