<?php

namespace Arielenter\ValidationAssertions\Tests\Unit;

use Arielenter\Validation\Assertions;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Session;
use PHPUnit\Framework\Attributes\Test;
use ValueError;
use function __;

class ValidationAssertionsTest extends ValidationAssertionsTestHelpers {

    use Assertions;

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
        [$a1, $a2, $a3, $a4, $a5] = [$this->exampleUrl, 'user_id_field',
            'not a number', 'numeric', 'delete'];

        $this->assertValidationRuleIsImplementedInUrl($a1, $a2, $a3, $a4,
                $a5);
    }

    #[Test]
    public function if_no_request_method_is_specified_post_is_assumed(): void {
        [$a1, $a2, $a3, $a4] = [$this->exampleUrl, 'username_field', '',
            'required'];

        $this->assertValidationRuleIsImplementedInUrl($a1, $a2, $a3, $a4);
    }

    #[Test]
    public function session_is_flush_after_every_assertion(): void {
        $this->will_pass_if_validation_is_implemented_in_url();

        $this->assertEmpty(Session::all());
    }

    #[Test]
    public function will_fail_if_validation_is_not_implemented_in_url(): void {
        [$a1, $a2, $a3, $a4] = [$this->exampleUrl, 'username_field',
            'confirmed is not implemented for username_field', 'confirmed'];

        $this->checkValidationAssertionThrowsExpectedError($a1, $a2, $a3, $a4);

        [$b1, $b2, $b3, $b4] = [$this->exampleUrl, 'non_implemented_field', '',
            'required'];

        $this->checkValidationAssertionThrowsExpectedError($b1, $b2, $b3, $b4);

        [$c1, $c2, $c3, $c4] = ['/non-existent-url', 'username_field', '',
            'required'];

        $c5 = "Session is missing expected key [errors].\n"
                . "Failed asserting that false is true.";

        $this->checkValidationAssertionThrowsExpectedError($c1, $c2, $c3, $c4,
                $c5);
    }

    #[Test]
    public function instances_of_validation_rule_can_be_used(): void {
        [$a1, $a2, $a3, $a4] = [$this->exampleUrl, 'password_field', 'short',
            $this->passowrdRuleInstance];

        $this->assertValidationRuleIsImplementedInUrl($a1, $a2, $a3, $a4);
    }

    #[Test]
    public function files_can_be_used_as_an_invalid_value(): void {
        [$a1, $a2, $a3, $a4] = [$this->exampleUrl, 'username_field',
            UploadedFile::fake()->image('avatar.jpg'), 'string'];

        $this->assertValidationRuleIsImplementedInUrl($a1, $a2, $a3, $a4);
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
    public function error_is_thrown_if_value_example_is_not_invalid(): void {
        [$a1, $a2, $a3, $a4] = [$this->exampleUrl, 'username_field',
            'not empty', 'required'];

        $replace = [
            'data' => json_encode([$a2 => $a3]),
            'rule' => json_encode([$a2 => $a4])
        ];

        $this->assertThrows(
                fn() => $this->assertValidationRuleIsImplementedInUrl($a1,
                        $a2, $a3, $a4),
                Exception::class,
                __("{$this->transPrefix}.not_invalid_data", $replace)
        );
    }

    #[Test]
    public function only_supported_request_methods_are_permited(): void {
        [$a1, $a2, $a3, $a4, $a5] = [$this->exampleUrl, 'user_id_field',
            'not a number', 'numeric', 'unsupported method'];

        $supportedMethods = ['get', 'post', 'put', 'patch', 'delete',
            'options'];

        $replace = [
            'method' => $a5,
            'supported_methods' => json_encode($supportedMethods)
        ];

        $this->assertThrows(
                fn() => $this->assertValidationRuleIsImplementedInUrl($a1,
                        $a2, $a3, $a4, $a5),
                ValueError::class,
                __("{$this->transPrefix}.unsupported_request_method",
                        $replace)
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
                ValueError::class,
                __("{$this->transPrefix}.incorrect_rule_value", $replace)
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
         * The following regex rule example must be established as an array 
         * since it contains a pipe | in it, which are used in delimited 
         * strings composed rules like the following one: numeric|max:100. For 
         * this reason if the regex example were not to be encapsulated into an 
         * array, the validator confuses it as two separated rules which won't 
         * work.
         * 
         */
        $this->assertValidationRulesAreImplementedInUrl(
                $this->exampleUrl,
                [
                    [
                        'username_field',
                        ['0invalid', 'inva..lid', 'invalid.', 'inv@lid'],
                        [$this->regexRuleExample]
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
                        [$this->regexRuleExample]
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
}
