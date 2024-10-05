<?php

return [
    "not_invalid_data" => "Unable to produce a validation error message from "
    . "the given invalid data example when confronted against its paired "
    . "validation rule. Data :data Rule: :rule",
    
    "validation_assertion_failed" => "Fail asserting that submitting the "
    . "following data to ':url' using the method ':method' returnes back the "
    . "expected error inside the error bag ':error_bag'.\n"
    . "Data: :data\n"
    . "Rule being tested: :rule\n"
    . "Expected Validation Error: :expected_validation_error\n"
    . "assertSessionHasErrorsIn Said: :assert_session_has_errors_in_fail",
    
    "row_should_had_been_a_nested_array" => "The row of the list with key "
    . "':key' and value :value is not an array but a ':type'. All items of the "
    . "list must be nested arrays with integer keys 0, 1 and 2.",
    
    "row_has_a_missing_key" => "The row of the list with key ':row_key' and "
    . "value :value must had have 3 numeric keys of its own: 0 for Field(s), "
    . "1 for Value(s) and 2 for the Validation Rule. Instead we were unable to "
    . "find key ':missing_key'.",
    
    "incorrect_rule_value_type" => "Rule :rule has an incorrect value type. "
    . ":value is a ':type' but only ':correct_types' are allowed.",
    
    "incorrect_object_rule" => "Rule :rule is not an instance of "
    . ":classes.",
    
    "wrong_field_name_value_type" => "Field name given with key ':field_key' "
    . "and value :field_name of row with key ':row_key' and value :row_value "
    . "should had been a string. Instead a :actual_type was given instead.",
    
    
    "unsupported_request_method" => "The given request method ':method' is not "
    . "supported. The only supported methods are the following: "
    . ":supported_methods.",
    
    "unknown_rule_given" => "Rule given :rule doesn't appear to be a known "
    . "possible rule name. Make sure you are written the rule name correctly. "
    . "The 'validator()' function returned the following error message: "
    . ":validator_error"
];
