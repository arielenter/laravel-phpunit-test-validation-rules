[Español](https://github.com/arielenter/laravel-phpunit-test-validation-rules/blob/main/README.es.md)

# **Package for Laravel Phpunit validation rules testing.**

## Description

Trait to be used within TestCase’s tests. It provides assertions to check if 
a given validation rule is implemented in a given URL or route name for a 
given request method. One of its more attractive functionality is that it’s 
possible to test multiple validation rules on one assertion instruccion.

## How it works

A desired validation rule is tested by submitting a provided invalid field 
value example to a given URL or route name using an established request method 
and asserting that the expected error message is returned from it. No need to 
provided the expected error thought. See Assertions Code In A Nutshell section 
to check in brief how exactly the code does this.

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
    $regexRule = 'regex:/^[a-z]([a-z0-9]|[a-z0-9]\.[a-z0-9])*$/i';
    $request->validate([
        'username_field' => ['required', 'string', 'max:20', $regexRule],
        'password_field' => Password::min(6),
        'same_max_field' => 'max:20',
        'same_regex_field' => [$regexRule],
    ]);
});

/**
 * Unique extended requests for each route with defined rule and errorBag 
 * properties could have also been used as well. I just felt this was fine as 
 * a quick simple example.
 * 
 */


```

It would be necessary to have the following tests to make sure all the 
desired validations are in place:

### RoutesValidationTest.php

```php
<?php

namespace Tests\Feature;

use Arielenter\Validation\Assertions as ValidationAssertions;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class RoutesValidationTest extends TestCase {

    use ValidationAssertions;

    public function test_single_validation_rule_in_patch_url() {
        $this->assertValidationRuleIsImplementedInUrl('/patch',
                'accept_field', '', 'required', 'patch', 'patch_error_bag');
//         arguments: $url, $fieldName, $invalidValueExample, 
//         $validationRule, $requestMethod = 'post', $errorBag = 'default'
    }

    public function
    test_all_rules_exhaustively_one_rule_at_a_time_in_delete_url_and_route() {
        $this->assertValidationRuleIsImplementedInUrl('/delete',
                'user_id_field', 'not numeric', 'numeric', 'delete');

        $this->assertValidationRuleIsImplementedInRouteName('delete_route',
                'user_id_field', '101', ['numeric', 'max:100'], 'delete');
//      'numeric|max:100' could also had been used here
    }

    public function test_all_rules_exhaustively_in_post_url_all_at_once() {
        $file = UploadedFile::fake()->image('avatar.jpg');
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
/**
 *                      regex has to be nested inside an array because it 
 *                      contains a pipe | on it, otherwise it will be confuse 
 *                      as a composed string rule (example ‘numeric|max:100’)                      
*/
                        ['regex:/^[a-z]([a-z0-9]|[a-z0-9]\.[a-z0-9])*$/i']
                    ],
                    ['password_field', 'short', Password::min(6)]
                ]
        );
//      assertValidationRulesAreImplementedInRouteName is also available
    }
}

```

### Argument ‘list’ array shape

Though I believe that the last example to test multiple rules in one 
assertion says more than a thousand words on it self, I decided it was still a 
good idea to include a PHPDoc explaining how the ‘list’ argument must be 
formatted:

```php
    /**
     * @param array<array> \$list List of arrays where validation rules are 
     * paired with invalid data examples for them. This nested arrays must have 
     * the following 3 keys: 0 for Field(s), 1 for Invalid Value Example(s) and 
     * lastly 2 for the Validation Rule desired to be tested. Key 0 and 1 can 
     * have multiple field names and invalid value examples respectively by 
     * nesting them inside an array. Field names must always be string values.
     * Composed validation rules can be given either as a pipe | delimited 
     * string (example ‘numeric|max:100’) or an array (example 
     * [‘numeric’, ‘max:100’]). Rules can only be string values or instances
     * of Illuminate\Contracts\Validation\Rule. Array shape:
     * array<array{
     *      0: string|array<string>,
     *      1: mixed|array<mixed|array<mixed>>,
     *      2: string|Rule|array<string|Rule>
     * }>
     * 
     */
```

## Assetions Code In A Nutshell

A brief explanation on how this assertions were coded is that the following 
function is used to get the expected fail validation message:

```php
validator($data, $rule)->messages()->first();
```

And once the expected fail validation error message is known, it’s then used 
to check if said message is returned when submitting the invalid data to the 
given URL using an already existent TestCase request function like the 
following and using one of it’s also already existenting assertions:

```php
$this->post($uri, $data)->assertInvalid($errors, $errorBag);
```

The following is a quick example code that shows in a nutshell how the 
assertions were made.

### AssertionsCodeInANutshellTest.php

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
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
    ): void {
        $fieldValue = [$fieldName => $invalidValueExample];
        $fieldRule = [$fieldName => $validationRule];

        $expectedErrorMsg = validator($fieldValue, $fieldRule)->messages()
                ->first();

        $fieldError = [$fieldName => $expectedErrorMsg];

        $this->$requestMethod($url, $fieldValue)
                ->assertInvalid($fieldError, $errorBag);
    }

    public function test_assertions_code_in_a_nutshell(): void {
        $this->validationAssertionsInANutshell('/patch', 'accept_field', '',
                'required', 'patch', 'patch_error_bag');
    }
}

```

## Additional Information

### Supported TestCase Request Methods

As described in the paragraphs above, Phpunit’s TestCase request methods are 
used to make the assertions, to this end, you can select which method you 
desire to be used by sending the ‘requestMethod’ argument with one of the 
following strings:

+ get
+ post
+ put
+ patch
+ delete
+ options
+ getJson
+ postJson
+ putJson
+ patchJson
+ deleteJson
+ optionsJson


The ‘requestMethod’ argument is flexible, meaning it’s case insensitive and 
other naming conventions could be used and it will work the same (example 
‘post-json’ instead of ‘postJson’).

### About TestCase ‘get’ Request Method

In case you are wondering, if you were to use the ‘get’ method or it’s json 
variant ‘getJson’, it won’t be necessary to include parameters on it’s URL, 
this would be handle by the assertion on its own.

### Additional Arguments Explanation

Phpunit’s TestCase request methods have an optional argument ‘headers’, to 
this end, an optional ‘header’ argument is also available.

Phpunit’s TestCase Json type request methods have also an optional argument 
‘options’, and for that specific reason ‘options’ is also available, thought it 
will be ignore if the given method is not a json type.

## License

GNU General Public License (GPL) version 3