<?php

namespace Arielenter\ValidationAssertions\Tests\Feature;

use Arielenter\Validation\Assertions as ValidationAssertions;
use Arielenter\ValidationAssertions\Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class RoutesValidationTest extends TestCase {

    use ValidationAssertions;

    public function test_single_validation_rule_in_route_name() {
        $this->assertValidationRuleIsImplementedInUrl('/patch',
                'accept_field', '', 'required', 'patch', 'patch_error_bag');
//         :arguments: $routeName, $fieldName, $invalidValueExample, 
//         $validationRule, $requestMethod = 'post', $errorBag = 'default'
    }

    public function test_all_rules_exhaustively_in_url_one_rule_at_a_time() {
        $this->assertValidationRuleIsImplementedInUrl('/delete',
                'user_id_field', 'not numeric', 'numeric', 'delete');

        $this->assertValidationRuleIsImplementedInRouteName('delete_route',
                'user_id_field', '101', ['numeric', 'max:100'], 'delete');
//      'numeric|max:100' :could_also_had_been_used_here
    }

    public function test_all_rules_exhaustively_in_url_all_at_once() {
        $file = UploadedFile::fake()->image('avatar.jpg');
        $regex = ['regex:/^[a-zA-Z]([a-zA-Z0-9]|[a-zA-Z0-9]\.[a-zA-Z0-9])*$/'];
//      :regex_has_to_be_nested_inside_an_array
        $tooLong = Str::repeat('x', 21);
        $this->assertValidationRulesAreImplementedInUrl(
                '/post',
                [
                    ['username_field', '', 'required'],
                    ['username_field', $file, 'string'],
                    [['username_field', 'same_max_field'], $tooLong, 'max:20'],
                    [
                        ['username_field', 'same_regex_field'],
                        ['0invalid', 'inva..lid', 'invalid.', 'inv@lid'],
                        $regex
                    ],
                    ['password_field', 'short', Password::min(6)]
                ]
        );
//      assertValidationRulesAreImplementedInRouteName :is_also_available
    }
}
