<?php

return [
    'not_invalid_data' => 'No fue posible producir el mensaje de validación '
    . 'fallida utilizando el ejemplo de valor incorrecto otorgado cuando éste '
    . 'es confrontado con la regla de validación emparejada con el mismo. '
    . 'Valor incorrecto: :data Regla de Validación: :rule',
    
    'validation_assertion_failed' => 'No fue posible afirmar que enviando la '
    . 'siguiente información a ‘:url’ utilizando el método de petición '
    . '‘:method’:with_headers regreso de vuelta el error esperado dentro de la '
    . 'bolsa de error ‘:error_bag’.' . "\n"
    . 'Información enviada: :data' . "\n"
    . 'Regla por comprobar: :rule' . "\n"
    . 'Error esperado: :expected_validation_error' . "\n"
    . 'assertInvalid dice: :assert_invalid_fail_msg',
    
    'with_headers' => ' con los encabezados siguientes ‘:headers’',
    
    'row_should_had_been_a_nested_array' => 'La fila de la lista con la llave '
    . '‘:key’ y con valor :value no es de tipo arreglo si no tipo ‘:type’. '
    . 'Todas las filas de la lista deben ser arreglos anidados con tres llaves '
    . 'numéricas 0, 1 y 2.',
    
    'row_has_a_missing_key' => 'La fila de la lista con la llave ‘:row_key’ y '
    . 'el valor :value debía tener sus propias tres llaves numéricas: 0 para '
    . 'el Campo(s), 1 para el Valor(es) y 2 para la regla de validación. Sin '
    . 'embargo la llave numérica ‘:missing_key’ no existe.',
    
    'incorrect_rule_value_type' => 'La regla :rule tiene un tipo de valor '
    . 'incorrecto. :value tiene un tipo de valor ‘:type’ pero solo los '
    . 'siguientes tipos son admitidos ‘:correct_types’',
    
    'incorrect_object_rule' => 'La regla :rule no es una instancia de '
    . ':classes.',
    
    'wrong_field_name_value_type' => 'El nombre de campo con llave '
    . '‘:field_key’ y el valor :field_name de la fila con llave ‘:row_key’ y '
    . 'el valor :row_value debió ser una cadena de caracteres. Sin embargo el '
    . 'valor proporcionado es de tipo :actual_type.',
    
    'unsupported_request_method' => 'El método de petición proporcionado '
    . '‘:method’ no esta soportado. Los únicos métodos soportados son los '
    . 'siguientes: :supported_methods.',
    
    'unknown_rule_given' => 'La regla de validación proporcionada :rule no '
    . 'parece ser una regla conocida. Cerciorate que la estas escribiendo '
    . 'correctamente. La función ‘validator()’ retorno el siguiente error: '
    . ':validator_error'
];
