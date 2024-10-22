[English](https://github.com/arielenter/laravel-phpunit-test-validation-rules/blob/main/README.md)

# **Paquete de Laravel y Phpunit para probar si las reglas de validación proporcionadas están implementadas o no**

## Description

Esta rasgo esta hecho para ser implementado en pruebas TestCase. Proporciona 
afirmaciones que permiten probar si la(s) regla(s) de validación 
proporcionada(s) esta implementada(s) o no en un URL o nombre de ruta dado en 
un método de petición establecido. Una de las funciones más llamativa es la de 
probar varias reglas de validación en una sola afirmación.

## ¿Cómo es que funciona?

La regla de validación deseada se prueba enviando el ejemplo de valor 
incorrecto proporcionado al URL o nombre de ruta establecido utilizando el 
método de petición dado, y afirmando que el mensaje de validación fallida 
esperado es recibido de vuelta. No es necesario proporcionar el mensaje de 
error. Si desea saber de manera breve como funciona el código, vaya a la 
sección titulada ‘Código en breve’.

## Instalación

```bash
composer require --dev arielenter/laravel-phpunit-test-validation-rules
```

## ¿Cómo se usa?

Digamos que las siguientes rutas han sido establecidas:

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
 * También es posible utilizar peticiones extendidas con sus propias reglas de 
 * validación y bolsa de error, pero evite usarlas para simplificar este 
 * ejemplo.
 * 
 */


```

Seria necesario realizar las siguientes pruebas para comprobar que todas las 
reglas de validación deseadas han sido implementadas correctamente.

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
//         argumentos: $url, $fieldName, $invalidValueExample, 
//         $validationRule, $requestMethod = 'post', $errorBag = 'default'
    }

    public function
    test_all_rules_exhaustively_one_rule_at_a_time_in_delete_url_and_route() {
        $this->assertValidationRuleIsImplementedInUrl('/delete',
                'user_id_field', 'not numeric', 'numeric', 'delete');

        $this->assertValidationRuleIsImplementedInRouteName('delete_route',
                'user_id_field', '101', ['numeric', 'max:100'], 'delete');
//      'numeric|max:100' también se pudo haber usado
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
 *                      regex debe ser encapsulada en un arreglo debido a la 
 *                      pipa |, ya que de otra forma seria confundida con una 
 *                      regla compuesta (ejemplo ‘numeric|max:100’).                      
*/
                        ['regex:/^[a-z]([a-z0-9]|[a-z0-9]\.[a-z0-9])*$/i']
                    ],
                    ['password_field', 'short', Password::min(6)]
                ]
        );
//      assertValidationRulesAreImplementedInRouteName también esta disponible
    }
}

```

### Formato del argumento de tipo arreglo ‘lista’

Considero que el ejemplo anterior donde se prueban múltiples reglas de 
validación en una sola afirmación habla más que mil palabras por sí mismo. Sin 
embargo de igual forma considere escribir una explicación al respecto, la cual 
puede ser traducida del ingles al español de la siguiente forma:

```php
    /**
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
     */
```

## Código en breve

En palabras sencillas la manera en que estas afirmaciones fueron programadas 
es que la siguiente función es utilizada para obtener el mensaje de validación 
fallida esperado.

```php
validator($data, $rule)->messages()->first();
```

Y una vez que se conoce el mensaje de validación fallida, se comprueba 
que dicho mensaje es recibido de regreso al enviar los valores inválidos 
proporcionados al URL dado. Para dicho propósito se utiliza alguno de los 
métodos de petición preexistentes en la instancia TestCase así como una 
afirmación que igualmente ya existe en dicha instancia:

```php
$this->post($uri, $data)->assertInvalid($errors, $errorBag);
```

Lo siguiente es una versión abreviada del código que realiza las 
afirmaciones:

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

## Información Adicional

### Métodos de Petición TestCase Soportados

Como se describió en los párrafos anteriores, los métodos de petición de 
Phpunit TestCase son utilizados para realizar las afirmaciones, con este 
propósito, puedes escoger alguno de los siguientes métodos enviando la cadena 
que corresponda en el argumento ‘requestMethod’.

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


El argumento ‘requestMethod’ es flexible, de manera que la cadena enviada 
puede estar formateada en mayúsculas o minúsculas sin distinción, además, es 
posible utilizar otras convenciones para nombrarlas (ejemplo ‘post-json’ en 
lugar de ‘postJson’.

### Acerca del método de petición TestCase ‘get’

En caso de que te lo preguntases, si planeas usar el método ‘get’ o su 
variante ‘getJson’ no sera necesario enviar los parámetros como parte del URL, 
ya que las afirmaciones se encargaran de ello por sí mismas.

### Explicación de Argumentos Adicionales

Los métodos de petición Phpunit TestCase poseen la posibilidad de enviar 
encabezados durante las pruebas, con este mismo propósito existe el argumento 
‘headers’.

Para métodos Phpunit TestCase de tipo Json existe la posibilidad de establecer 
opciones, con este propósito existe el argumento ‘options’, sin embargo, tenga 
en cuenta que si el método de petición proporcionado en el argumento 
‘requestMethod’ no es de tipo Json el argumento ‘options’ simplemente sera 
ignorado.

## Licencia

GNU General Public License (GPL) version 3