# **:main_title**

## :description

:description_paragraph

## :how_it_works

:how_it_works_paragraph

## :installation

```bash
composer require --dev arielenter/laravel-phpunit-test-validation-rules
```

## :usage

:say_the_following_routes_are_implemented

### :routes_file

```php
:routes_file_content
```

:then_this_tests_should_be_made

### :tests_file

```php
:tests_file_content
```

## :assertions_code_in_a_nuteshell

:function_used_to_get_validation_fail_error

```php
validator($data, $rule)->messages()->first();
```

:testcase_request_method_and_assertion_used

```php
$this->post($uri, $data)->assertSessionHasErrorsIn($errorBag, $keys);
```

:this_is_a_quick_example_code

### :code_file

```php
:code_file_content
```