<?php

return [
    "not_invalid_data" => "No fue posible producir el mensaje de validación "
    . "fallida utilizando el ejemplo de valor incorrecto otorgado cuando éste "
    . "es confrontado con la regla de validación emparejada con el mismo. "
    . "Valor incorrecto: :data Regla de Validación: :rule",
    
    "validation_assertion_failed" => "No fue posible afirmar que enviando la "
    . "siguiente información a ':url' utilizando el método de petición "
    . "':method' regreso de vuelta el error esperado dentro de la bolsa de "
    . "error ':error_bag'.\n"
    . "Información enviada: :data\n"
    . "Regla por comprobar: :rule\n"
    . "Error esperado: :expected_validation_error\n"
    . "assertSessionHasErrorsIn dice: :assert_session_has_errors_in_fail",
    
    "row_should_had_been_a_nested_array" => "La fila de la lista con la llave "
    . "':key' y con valor :value no es de tipo arreglo si no tipo ':type'. "
    . "Todas las filas de la lista deben ser arreglos anidados con tres llaves "
    . "numéricas 0, 1 y 2.",
    
    "row_has_a_missing_key" => "La fila de la lista con la llave ':row_key' y "
    . "el valor :value debía tener sus propias tres llaves numéricas: 0 para "
    . "el Campo(s), 1 para el Valor(es) y 2 para la regla de validación. Sin "
    . "embargo la llave numérica ':missing_key' no existe.",
    
    "incorrect_rule_value_type" => "La regla :rule tiene un tipo de valor "
    . "incorrecto. :value tiene un tipo de valor ':type' pero solo los "
    . "siguientes tipos son admitidos ':correct_types'",
    
    "incorrect_object_rule" => "Rule :rule is not an instance of "
    . ":classes.",
    
    "wrong_field_name_value_type" => "Field name given with key ':field_key' "
    . "and value :field_name of row with key ':row_key' and value :row_value "
    . "should had been a string. Instead a :actual_type was given instead.",
    
    "unsupported_request_method" => "The given request method ':method' is not "
    . "supported. The only supported methods are the following: "
    . ":supported_methods.",
    
    "incorrect_rule_value" => "Rule given :rule doesn't appear to be a known "
    . "possible rule name. Make sure you are written the rule name correctly. "
    . "The 'validator()' function returned the following error message: "
    . ":validator_error"
];
