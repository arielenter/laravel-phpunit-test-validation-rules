# **Package for Laravel Phpunit validation rules testing.**

## Description

TestCase trait that provides assertions to test that given validation rules are implemented in a given URL or route name for a given request method.

## How it works

A desired validation rule is tested by submitting a provided invalid field value example to a given URL or route name using an established request method and asserting that the expected error message is returned from it. No need to provided the expected error thought. See Assertions Code In A Nutshell section to check in brief how exactly the code does this.

## Installation

```bash
composer require --dev arielenter/laravel-phpunit-test-validation-rules
```

## Usage

Say the following routes are in place:

### web.php

```php
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rules\Password;

Route::patch('/patch', function (Request $request) {
    $request->validateWithBag('patch_error_bag',
            ['accept_field' => 'required']);
});

Route::delete('/delete', function (Request $request) {
    $request->validate(['user_id_field' => 'numeric|max:100']);
})->name('delete_route');

Route::post('/post', function (Request $request) {
    $regexRule = 'regex:/^[a-zA-Z]([a-zA-Z0-9]|[a-zA-Z0-9]\.[a-zA-Z0-9])*$/';
    $request->validate([
        'username_field' => ['required', 'string', 'max:20', $regexRule],
        'password_field' => [Password::min(6)],
        'same_max_field' => 'max:20',
        'same_regex_field' => [$regexRule],
    ]);
});

/**
 * Unique extended requests for each route with defined rule and errorBag 
 * properties could have also been used as well. I just felt this was fine as a 
 * quick simple example.
 * 
 */


```

It would be necessary to have the following tests to make sure all the desired validations are in place:

### RoutesValidationTest.php

```php
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
//         arguments: $routeName, $fieldName, $invalidValueExample, 
//         $validationRule, $requestMethod = 'post', $errorBag = 'default'
    }

    public function test_all_rules_exhaustively_in_url_one_rule_at_a_time() {
        $this->assertValidationRuleIsImplementedInUrl('/delete',
                'user_id_field', 'not numeric', 'numeric', 'delete');

        $this->assertValidationRuleIsImplementedInRouteName('delete_route',
                'user_id_field', '101', ['numeric', 'max:100'], 'delete');
//      'numeric|max:100' could also had been used here
    }

    public function test_all_rules_exhaustively_in_url_all_at_once() {
        $file = UploadedFile::fake()->image('avatar.jpg');
        $regex = ['regex:/^[a-zA-Z]([a-zA-Z0-9]|[a-zA-Z0-9]\.[a-zA-Z0-9])*$/'];
//      regex has to be nested inside an array since it contains a pipe on it
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
//      assertValidationRulesAreImplementedInRouteName is also available
    }
}

```

## Assetions Code In A Nutshell

In brief, the following function is used to get the fail validation message:

```php
validator($data, $rule)->messages()->first();
```

Once the fail validation error message is known, it is used to check if said message is returned when submitting the invalid data to the given URL using an already existent TestCase request function like the following and using one of it’s also already existent assertions.

```php
$this->post($uri, $data)->assertSessionHasErrorsIn($errorBag, $keys);
```

The following is a quick example code that shows in a nutshell how the assertions were made.

### AssertionsCodeInANutshellTest.php

```php
<?php

namespace Tests\Feature;

use Arielenter\ValidationAssertions\Tests\TestCase;
use Illuminate\Validation\Rule;
use function validator;

class AssertionsCodeInANutshellTest extends TestCase {

    public function validationAssertionsInANutshell(
            string $url,
            string $fieldName,
            mixed $invalidValueExample,
            string|Rule|array $validationRule,
            string $requestMethod = 'post',
            string $errorBag = 'default'
    ) {
        $fieldValue = [$fieldName => $invalidValueExample];
        $fieldRule = [$fieldName => $validationRule];

        $expectedErrorMsg = validator($fieldValue, $fieldRule)->messages()
                ->first();

        $fieldError = [$fieldName => $expectedErrorMsg];

        $this->$requestMethod($url, $fieldValue)
                ->assertSessionHasErrorsIn($errorBag, $fieldError);
    }

    public function test_assertions_code_in_a_nutshell(): void {
        $this->validationAssertionsInANutshell('/patch', 'accept_field', '',
                'required', 'patch', 'patch_error_bag');
    }
}

```