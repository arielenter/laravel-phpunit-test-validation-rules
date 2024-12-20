<?php

return [
    'other_languages' => '[Español](https://github.com/arielenter/'
    . 'laravel-phpunit-test-validation-rules/blob/main/README.es.md)',
    
    'main_title' => 'Package for Laravel Phpunit validation rules testing.',
    
    'description' => 'Description',
    
    'description_paragraph' => 
'Trait to be used within TestCase’s tests. It provides assertions to check if 
a given validation rule is implemented in a given URL or route name for a 
given request method. One of its more attractive functionality is that it’s 
possible to test multiple validation rules on one assertion instruccion.',
    
    'how_it_works' => 'How it works',
    
    'how_it_works_paragraph' => 
'A desired validation rule is tested by submitting a provided invalid field 
value example to a given URL or route name using an established request method 
and asserting that the expected error message is returned from it. No need to 
provided the expected error thought. See Assertions Code In A Nutshell section 
to check in brief how exactly the code does this.',
    
    'installation' => 'Installation',
    
    'usage' => 'Usage',
    
    'say_the_following_routes_are_implemented' => 
'Say the following routes are in place:',
    
    'then_this_tests_should_be_made' => 
'It would be necessary to have the following tests to make sure all the 
desired validations are in place:',
    
    'list_array_shape' => 'Argument ‘list’ array shape',
    
    'the_following_explains_the_correct_shape' => 
'Though I believe that the last example to test multiple rules in one 
assertion says more than a thousand words on it self, I decided it was still a 
good idea to include a PHPDoc explaining how the ‘list’ argument must be 
formatted:',
    
    'array_shape_phpdoc' => 
'    /**
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
     */',
    
    'assertions_code_in_a_nuteshell' => 'Assetions Code In A Nutshell',
    
    'function_used_to_get_validation_fail_error' => 
'A brief explanation on how this assertions were coded is that the following 
function is used to get the expected fail validation message:',
    
    'testcase_request_method_and_assertion_used' => 
'And once the expected fail validation error message is known, it’s then used 
to check if said message is returned when submitting the invalid data to the 
given URL using an already existent TestCase request function like the 
following and using one of it’s also already existenting assertions:',
    
    'this_is_a_quick_example_code' => 
'The following is a quick example code that shows in a nutshell how the 
assertions were made.',
    
    'additional_information' => 'Additional Information',
    
    'supported_methods_title' => 'Supported TestCase Request Methods',
    
    'supported_methods_paragraph_1' => 
'As described in the paragraphs above, Phpunit’s TestCase request methods are 
used to make the assertions, to this end, you can select which method you 
desire to be used by sending the ‘requestMethod’ argument with one of the 
following strings:',
    
    'supported_methods_paragraph_2' => 
'The ‘requestMethod’ argument is flexible, meaning it’s case insensitive and 
other naming conventions could be used and it will work the same (example 
‘post-json’ instead of ‘postJson’).',
    
    
    'about_get_request_method' => 'About TestCase ‘get’ Request Method',
    
    'about_get_request_method_paragraph' => 
'In case you are wondering, if you were to use the ‘get’ method or it’s json 
variant ‘getJson’, it won’t be necessary to include parameters on it’s URL, 
this would be handle by the assertion on its own.',
    
    'additional_arguments_title' => 'Additional Arguments Explanation',
    
    'additional_arguments_explanation_paragraph_1' => 
'Phpunit’s TestCase request methods have an optional argument ‘headers’, to 
this end, an optional ‘header’ argument is also available.',
    
    'additional_arguments_explanation_paragraph_2' => 
'Phpunit’s TestCase Json type request methods have also an optional argument 
‘options’, and for that specific reason ‘options’ is also available, thought it 
will be ignore if the given method is not a json type.',
    
    'license' => 'License'
];
