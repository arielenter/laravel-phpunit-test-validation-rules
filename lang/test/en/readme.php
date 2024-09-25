<?php

return [
    "other_languages" => "[Español](https://github.com/arielenter/"
    . "laravel-phpunit-test-validation-rules/blob/main/README.es.md)",
    
    "main_title" => "Package for Laravel Phpunit validation rules testing.",
    
    "description" => "Description",
    
    "description_paragraph" => "Trait to be used within TestCase's tests. It "
    . "provides assertions to check if a given validation rule is implemented "
    . "in a given URL or route name for a given request method. One of its "
    . "more attractive functionality is that it's possible to test multiple "
    . "validation rules on one assertion instruccion.",
    
    "how_it_works" => "How it works",
    
    "how_it_works_paragraph" => "A desired validation rule is tested by "
    . "submitting a provided invalid field value example to a given URL or "
    . "route name using an established request method and asserting that the "
    . "expected error message is returned from it. No need to provided the "
    . "expected error thought. See Assertions Code In A Nutshell section to "
    . "check in brief how exactly the code does this.",
    
    "installation" => "Installation",
    
    "usage" => "Usage",
    
    "say_the_following_routes_are_implemented" => "Say the following routes "
    . "are in place:",
    
    "then_this_tests_should_be_made" => "It would be necessary to have the "
    . "following tests to make sure all the desired validations are in place:",
    
    "assertions_code_in_a_nuteshell" => "Assetions Code In A Nutshell",
    
    "function_used_to_get_validation_fail_error" => "In brief, the following "
    . "function is used to get the fail validation message:",
    
    "testcase_request_method_and_assertion_used" => "Once the fail validation "
    . "error message is known, it is used to check if said message is returned "
    . "when submitting the invalid data to the given URL using an already "
    . "existent TestCase request function like the following and using one of "
    . "it’s also already existent assertions:",
    
    "this_is_a_quick_example_code" => "The following is a quick example code "
    . "that shows in a nutshell how the assertions were made."
];
