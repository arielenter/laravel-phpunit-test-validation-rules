<?php

return [
    "other_languages" => "[English](https://github.com/arielenter/"
    . "laravel-phpunit-test-validation-rules/blob/main/README.md)",
    
    "main_title" => "Paquete de Laravel y Phpunit para probar si las reglas de "
    . "validación proporcionadas están implementadas o no",
    
    "description" => "Description",
    
    "description_paragraph" => "Esta rasgo esta hecho para ser implementado en "
    . "pruebas TestCase. Proporciona afirmaciones que permiten probar si la(s) "
    . "regla(s) de validación proporcionada(s) esta implementada(s) o no en un "
    . "URL o nombre de ruta dado en un método de petición establecido. Una de "
    . "las funciones más llamativa es la de probar varias reglas de validación "
    . "en una sola afirmación.",
    
    "how_it_works" => "¿Cómo es que funciona?",
    
    "how_it_works_paragraph" => "La regla de validación deseada se prueba "
    . "enviando el ejemplo de valor incorrecto proporcionado al URL o nombre "
    . "de ruta establecido utilizando el método de petición dado, y afirmando "
    . "que el mensaje de validación fallida esperado es recibido de vuelta. No "
    . "es necesario proporcionar el mensaje de error. Si desea saber de manera "
    . "breve como funciona el código, vaya a la sección titulada 'Código en "
    . "breve'.",
    
    "installation" => "Instalación",
    
    "usage" => "¿Cómo se usa?",
    
    "say_the_following_routes_are_implemented" => "Digamos que las siguientes "
    . "rutas han sido establecidas:",
    
    "then_this_tests_should_be_made" => "Seria necesario realizar las "
    . "siguientes pruebas para comprobar que todas las reglas de validación "
    . "deseadas han sido implementadas correctamente.",
    
    "assertions_code_in_a_nuteshell" => "Código en breve",
    
    "function_used_to_get_validation_fail_error" => "En palabras sencillas, la "
    . "siguiente función es utilizada para obtener el mensaje de validación "
    . "fallida esperado.",
    
    "testcase_request_method_and_assertion_used" => "Una vez que se conoce el "
    . "mensaje de validación fallida esperado, se comprueba que dicho mensaje "
    . "es recibido de regreso al enviar los valores inválidos proporcionados "
    . "al URL dado. Para dicho propósito se utiliza alguno de los metodos de "
    . "petición preexistentes en la clase TestCase así como una afirmación que "
    . "igualmente ya existe:",
    
    "this_is_a_quick_example_code" => "Lo siguiente es una versión abreviada "
    . "del código que realiza las afirmaciones:"
];
