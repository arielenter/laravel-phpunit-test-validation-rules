<?php

namespace Arielenter\ValidationAssertions\Tests\Support;

use function __;
use function trans;

trait TransAssertions {

    use TransAssertionsHelpers;

    /**
     * Checks if a given translation key actualy exist, if it does't it throws 
     * an assertion failed exception error, but if it does it returns the 
     * requested translation.
     * 
     * @param string $transKey
     * @param array|null $replace
     * @param string|null $locale
     * @return string|array
     */
    public function tryGetTrans(
            string $transKey,
            ?array $replace = [],
            ?string $locale = null
    ): string|array {
        $this->assertTrue(trans()->has($transKey, fallback: false),
                $this->getTransKeyMissingFailMsg($transKey, $locale));

        $trans = __($transKey, $replace, $locale);

        if (is_string($trans)) {
            $placeholders = $this->getPlaceholders($transKey, $locale);
            foreach ($placeholders as $placeholder) {
                $this->assertArrayHasKey($placeholder, $replace,
                        $this->getReplaceIsMissingPlaceholderFailMsg(
                                $placeholder, $transKey, $locale));
            }

            foreach ($replace as $replaceKey => $value) {
                $this->assertStringContainsString($value, $trans,
                        $this->getReplaceFailMsg($replaceKey, $transKey,
                                $locale));
            }
        }

        return $trans;
    }
}
