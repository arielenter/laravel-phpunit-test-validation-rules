<?php

return [
    'other_languages' => '[English](https://github.com/arielenter/'
    . 'laravel-phpunit-test-validation-rules/blob/main/README.md)',
    
    'main_title' => 'Paquete de Laravel y Phpunit para probar si las reglas de validación proporcionadas están implementadas o no',
    
    'description' => 'Description',
    
    'description_paragraph' => 
'Esta rasgo esta hecho para ser implementado en pruebas TestCase. Proporciona 
afirmaciones que permiten probar si la(s) regla(s) de validación 
proporcionada(s) esta implementada(s) o no en un URL o nombre de ruta dado en 
un método de petición establecido. Una de las funciones más llamativa es la de 
probar varias reglas de validación en una sola afirmación.',
    
    'how_it_works' => '¿Cómo es que funciona?',
    
    'how_it_works_paragraph' => 
'La regla de validación deseada se prueba enviando el ejemplo de valor 
incorrecto proporcionado al URL o nombre de ruta establecido utilizando el 
método de petición dado, y afirmando que el mensaje de validación fallida 
esperado es recibido de vuelta. No es necesario proporcionar el mensaje de 
error. Si desea saber de manera breve como funciona el código, vaya a la 
sección titulada ‘Código en breve’.',
    
    'installation' => 'Instalación',
    
    'usage' => '¿Cómo se usa?',
    
    'say_the_following_routes_are_implemented' => 
'Digamos que las siguientes rutas han sido establecidas:',
    
    'then_this_tests_should_be_made' => 
'Seria necesario realizar las siguientes pruebas para comprobar que todas las 
reglas de validación deseadas han sido implementadas correctamente.',
    
    'list_array_shape' => 'Formato del argumento de tipo arreglo ‘lista’',
    
    'the_following_explains_the_correct_shape' => 
'Considero que el ejemplo anterior donde se prueban múltiples reglas de 
validación en una sola afirmación habla más que mil palabras por sí mismo. Sin 
embargo de igual forma considere escribir una explicación al respecto, la cual 
puede ser traducida del ingles al español de la siguiente forma:',
    
    'array_shape_phpdoc' => 
'    /**
     * @param array<array> \$list Lista compuesta por arreglos en la que se 
     * emparejan reglas de validación con ejemplos de datos inválidos para las 
     * mismas. Estos arreglos deben tener tres llaves enteras: 0 para los/el 
     * nombre(s) de campo, 1 para los/el ejemplo(s) de valor invalido y 2 la 
     * regla de validación que se desea probar. Las llaves 0 y 1 pueden 
     * contener múltiples campos y múltiples ejemplos de valores inválidos 
     * respectivamente, para ello basta con anidarlos dentro de un arreglo. Los 
     * nombres de campo deben ser siempre de tipo cadena. Reglas compuestas 
     * pueden entregarse en una cadena separada por pipas (ejemplo 
     * ‘numeric|max:100’) o en un arreglo (ejemplo [‘numeric’, ‘max:100’]). 
     * Las reglas sólo podrán ser de tipo cadena o instancias de Illuminate\
     * Contracts\Validation\Rule. El formato de arreglo es el siguiente: 
     * array<array{ 
     *      0: string|array<string>, 
     *      1: mixed|array<mixed|array<mixed>>,
     *      2: string|Rule|array<string|Rule>
     * }>
     * 
     */',
    
    'assertions_code_in_a_nuteshell' => 'Código en breve',
    
    'function_used_to_get_validation_fail_error' => 
'En palabras sencillas la manera en que estas afirmaciones fueron programadas 
es que la siguiente función es utilizada para obtener el mensaje de validación 
fallida esperado.',
    
    'testcase_request_method_and_assertion_used' => 
'Y una vez que se conoce el mensaje de validación fallida, se comprueba 
que dicho mensaje es recibido de regreso al enviar los valores inválidos 
proporcionados al URL dado. Para dicho propósito se utiliza alguno de los 
métodos de petición preexistentes en la instancia TestCase así como una 
afirmación que igualmente ya existe en dicha instancia:',
    
    'this_is_a_quick_example_code' => 
'Lo siguiente es una versión abreviada del código que realiza las 
afirmaciones:',
    
    'additional_information' => 'Información Adicional',
    
    'supported_methods_title' => 'Métodos de Petición TestCase Soportados',
    
    'supported_methods_paragraph_1' => 
'Como se describió en los párrafos anteriores, los métodos de petición de 
Phpunit TestCase son utilizados para realizar las afirmaciones, con este 
propósito, puedes escoger alguno de los siguientes métodos enviando la cadena 
que corresponda en el argumento ‘requestMethod’.',
    
    'supported_methods_paragraph_2' => 
'El argumento ‘requestMethod’ es flexible, de manera que la cadena enviada 
puede estar formateada en mayúsculas o minúsculas sin distinción, además, es 
posible utilizar otras convenciones para nombrarlas (ejemplo ‘post-json’ en 
lugar de ‘postJson’.',
    
    'about_get_request_method' => 
'Acerca del método de petición TestCase ‘get’',
    
    'about_get_request_method_paragraph' => 
'En caso de que te lo preguntases, si planeas usar el método ‘get’ o su 
variante ‘getJson’ no sera necesario enviar los parámetros como parte del URL, 
ya que las afirmaciones se encargaran de ello por sí mismas.',
    
    'additional_arguments_title' => 'Explicación de Argumentos Adicionales',
    
    'additional_arguments_explanation_paragraph_1' => 
'Los métodos de petición Phpunit TestCase poseen la posibilidad de enviar 
encabezados durante las pruebas, con este mismo propósito existe el argumento 
‘headers’.',
    
    'additional_arguments_explanation_paragraph_2' => 
'Para métodos Phpunit TestCase de tipo Json existe la posibilidad de establecer 
opciones, con este propósito existe el argumento ‘options’, sin embargo, tenga 
en cuenta que si el método de petición proporcionado en el argumento 
‘requestMethod’ no es de tipo Json el argumento ‘options’ simplemente sera 
ignorado.',
    
    'license' => 'Licencia'
];
