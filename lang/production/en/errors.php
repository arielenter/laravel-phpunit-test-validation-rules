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
    
    "incorrect_object_rule" => "La regla :rule no es una instancia de "
    . ":classes.",
    
    "wrong_field_name_value_type" => "El nombre de campo con llave "
    . "':field_key' y el valor :field_name de la fila con llave ':row_key' y "
    . "el valor :row_value debió ser una cadena de caracteres. Sin embargo el "
    . "valor proporcionado es de tipo :actual_type.",
    
    "unsupported_request_method" => "El método de petición proporcionado "
    . "':method' no esta soportado. Los únicos métodos soportados son los "
    . "siguientes: :supported_methods.",
    
    "incorrect_rule_value" => "La regla de validación proporcionada :rule no "
    . "parece ser una regla conocida. Cerciorate que la estas escribiendo "
    . "correctamente. La función 'validator()' retorno el siguiente error: "
    . ":validator_error"
];
