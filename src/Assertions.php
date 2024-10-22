<?php

declare(strict_types=1);

namespace Arielenter\Validation;

use Arielenter\Validation\Exceptions\AssertionFailedException;
use Arielenter\Validation\Exceptions\IncorrectRuleValueTypeException;
use Arielenter\Validation\Exceptions\RowHasAMissingKeyException;
use Arielenter\Validation\Exceptions\RowShouldHadBeenANestedArrayException;
use Arielenter\Validation\Exceptions\UnknownRuleGivenException;
use Arielenter\Validation\Exceptions\UnsupportedRequestMethodException;
use Arielenter\Validation\Exceptions\WrongFieldNameValueTypeException;
use Illuminate\Contracts\Validation\Rule;
use function route;

/**
 * Assertion methods that help to test that the desired validation rule(s) are 
 * implemented in a given URL or route name.
 * 
 * @author Ariel Del Valle Lozano <arielmazatlan@gmail.com>
 * @copyright Copyright (c) 2024, Ariel Del Valle Lozano
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 
 * (GPL) version 3
 * @link https://github.com/arielenter/laravel-phpunit-test-validation-rules
 */
trait Assertions {

    public function assertValidationRuleIsImplementedInUrl(
            string $url,
            string $fieldName,
            mixed $invalidValueExample,
            string|Rule|array $validationRule,
            string $requestMethod = 'post',
            string $errorBag = 'default',
            array $headers = [],
            int $options = 0
    ): void {
        $validatedRequestMethod = UnsupportedRequestMethodException::
                validateRequestMethod($requestMethod);

        IncorrectRuleValueTypeException::
        ifRuleIsArrayValidateCorrectTypeOfItsValues($validationRule);

        $invalidDataExample = [$fieldName => $invalidValueExample];
        $fieldValidationRule = [$fieldName => $validationRule];

        $expectedErrorMessage = UnknownRuleGivenException::
                tryGetValidationErrorMessage($invalidDataExample,
                        $fieldValidationRule, $validationRule);

        AssertionFailedException::
        trySubmitInvalidDataExampleToUrlAndAssertItReturnsExpectedErrMsg($this,
                $url, $invalidDataExample, $fieldName, $fieldValidationRule,
                $expectedErrorMessage, $validatedRequestMethod, $errorBag,
                $headers, $options);
    }

    public function assertValidationRuleIsImplementedInRouteName(
            string $routeName,
            string $fieldName,
            mixed $invalidValueExample,
            string|Rule|array $validationRule,
            string $requestMethod = 'post',
            string $errorBag = 'default',
            array $headers = [],
            int $options = 0
    ): void {
        $this->assertValidationRuleIsImplementedInUrl(
                route($routeName),
                $fieldName,
                $invalidValueExample,
                $validationRule,
                $requestMethod,
                $errorBag,
                $headers,
                $options
        );
    }

    /**
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
     */
    public function
    assertValidationRulesAreImplementedInUrl(
            string $url,
            array $list,
            string $requestMethod = 'post',
            string $errorBag = 'default',
            array $headers = [],
            int $options = 0
    ): void {
        foreach ($list as $currentRowKey => $currentRow) {
            RowShouldHadBeenANestedArrayException::validateCurrentRowIsArray(
                    $currentRow, $currentRowKey);

            RowHasAMissingKeyException::
            validateCurrentRowHasIntKeysCeroOneAndTwo($currentRow,
                    $currentRowKey);

            $validationRule = $currentRow[2];
            IncorrectRuleValueTypeException
            ::validateKeyTwoValueType($validationRule, ['string', 'array']);

            $fieldNames = (is_array($currentRow[0])) ? $currentRow[0] :
                    array($currentRow[0]);

            $invalidValueExamples = (is_array($currentRow[1])) ?
                    $currentRow[1] : array($currentRow[1]);

            foreach ($fieldNames as $fieldKey => $fieldName) {
                WrongFieldNameValueTypeException::validateFieldNameIsString(
                        $fieldKey, $fieldName, $currentRowKey, $currentRow);

                foreach ($invalidValueExamples as $invalidValueExample) {
                    $this->assertValidationRuleIsImplementedInUrl($url,
                            $fieldName, $invalidValueExample, $validationRule,
                            $requestMethod, $errorBag, $headers, $options);
                }
            }
        }
    }

    /**
     * Same as assertValidationRulesAreImplementedInUrl but route names can be 
     * used instead of a URL. Check the aforementioned base assertion PHPDoc 
     * for more info about the correct array shape for the argument $list.
     * 
     */
    public function assertValidationRulesAreImplementedInRouteName(
            string $routeName,
            array $list,
            string $requestMethod = 'post',
            string $errorBag = 'default',
            array $headers = [],
            int $options = 0
    ): void {
        $this->assertValidationRulesAreImplementedInUrl(route($routeName),
                $list, $requestMethod, $errorBag, $headers, $options);
    }
}
