<?php

declare(strict_types=1);

namespace arielenter\Validation;

use Illuminate\Contracts\Validation\Rule;
use Orchestra\Testbench\TestCase;
use Illuminate\Support\Facades\Session;
use PHPUnit\Framework\AssertionFailedError;
use function __;
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
    use AssertionsHelper;

    public function assertValidationRuleIsImplementedInUrl(
            string $url,
            string $fieldName,
            mixed $invalidValueExample,
            string|Rule|array $validationRule,
            string $requestMethod = 'post',
            string $errorBag = 'default'
    ): void {
        $validatedRequestMethod = $this->validateRequestMethod($requestMethod);
        $this->ifRuleIsArrayValidateCorrectTypeOfItsValues($validationRule);

        $invalidDataExample = [$fieldName => $invalidValueExample];
        $fieldValidationRule = [$fieldName => $validationRule];
        $expectedErrorMessage = $this->getExpectedErrorMessage(
                $invalidDataExample,
                $fieldValidationRule,
                $validationRule
        );

        $this->submitInvalidDataExampleToUrlAndAssertItReturnsExpectedErrMsg(
                $url,
                $invalidDataExample,
                $fieldName,
                $fieldValidationRule,
                $expectedErrorMessage,
                $validatedRequestMethod,
                $errorBag
        );
    }

    private function
    submitInvalidDataExampleToUrlAndAssertItReturnsExpectedErrMsg(
            string $url,
            array $invalidDataExample,
            string $fieldName,
            array $fieldValidationRule,
            string $expectedErrorMessage,
            string $requestMethod,
            string $errorBag
    ): void {
        try {
            TestCase::$requestMethod($url, $invalidDataExample)
                    ->assertSessionHasErrorsIn($errorBag,
                            [$fieldName => $expectedErrorMessage]);
        } catch (AssertionFailedError $e) {
            $transKey = "{$this->transKeyPrefix}.validation_assertion_failed";

            $replace = [
                'url' => $url,
                'method' => $requestMethod,
                'error_bag' => $errorBag,
                'data' => json_encode($invalidDataExample),
                'rule' => json_encode($fieldValidationRule),
                'expected_validation_error' => $expectedErrorMessage,
                'assert_session_has_errors_in_fail' => $e->getMessage()
            ];

            TestCase::fail(__($transKey, $replace));
        } finally {
            Session::flush();
        }
    }

    public function assertValidationRuleIsImplementedInRouteName(
            string $routeName,
            string $fieldName,
            mixed $invalidValueExample,
            string|Rule $validationRule,
            string $requestMethod = 'post',
            string $errorBag = 'default'
    ): void {
        $this->assertValidationRuleIsImplementedInUrl(
                route($routeName),
                $fieldName,
                $invalidValueExample,
                $validationRule,
                $requestMethod,
                $errorBag
        );
    }

    /**
     * @param array<array> $list List of arrays where validation rules are 
     * paired with invalid data examples for them. This nested arrays must have 
     * the following 3 keys: 0 for Field(s), 1 for Invalid Value Example(s) and 
     * lastly 2 for the Validation Rule desired to be tested. Key 0 and 1 can 
     * have multiple field names and invalid value examples respectively by 
     * nesting them inside an array. Field names must always be string values.
     * Composed validation rules can be given either as a pipe | delimited 
     * string (example 'numeric|max:100') or an array (example 
     * ['numeric' => 'max:100']). Rules can only be string values or instances
     * of Illuminate\Contracts\Validation\Rule.
     * 
     */
    public function
    assertValidationRulesAreImplementedInUrl(
            string $url,
            array $list,
            string $requestMethod = 'post',
            string $errorBag = 'default'
    ): void {
        foreach ($list as $this->currentRowKey => $this->currentRow) {
            $this->validateRowArrayShape();

            $fieldValuePairs = $this->pairFieldsWithValues();
            $validationRule = $this->currentRow[2];

            foreach ($fieldValuePairs as $fieldValuePair) {
                [$fieldName, $invalidValueExample] = $fieldValuePair;

                $this->assertValidationRuleIsImplementedInUrl(
                        $url,
                        $fieldName,
                        $invalidValueExample,
                        $validationRule,
                        $requestMethod,
                        $errorBag
                );
            }
        }
    }

    /**
     * Same as assertValidationRulesAreImplementedInUrl but route names can be 
     * used instead of a URL. Check the aforementioned base assertion PHPDoc 
     * for more info in the correct list supported format.
     * 
     */
    public function assertValidationRulesAreImplementedInRouteName(
            string $routeName,
            array $list,
            string $requestMethod = 'post',
            string $errorBag = 'default'
    ): void {
        $this->assertValidationRulesAreImplementedInUrl(
                route($routeName), $list, $requestMethod, $errorBag);
    }
}
