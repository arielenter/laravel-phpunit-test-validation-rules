<?php

namespace Arielenter\ValidationAssertions\Tests\Support;

use Illuminate\Support\Facades\App;
use function __;

trait TransAssertionsHelper {

    private function getTransKeyMissingFailMsg(
            string $transKey,
            ?string $locale = null
    ): string {
        $locale ??= App::currentLocale();

        $errorMsg = "Translation key ':trans_key' doesn't exist for locale "
                . "':locale'.";

        return __($errorMsg, ['trans_key' => $transKey, 'locale' => $locale]);
    }

    private function getPlaceholders(
            string $transKey,
            ?string $locale = null
    ): array {
        preg_match_all('/::|:(([a-z_]|)+)/i', __($transKey, locale: $locale),
                $matches);

        return array_filter($matches[1]);
    }

    private function getReplaceIsMissingPlaceholderFailMsg(
            string $placeholder,
            string $transKey,
            ?string $locale = null
    ): string {
        $locale ??= App::currentLocale();

        $errorMsg = "Placeholder ':placeholder' is missing from the replace "
                . "array given for translation key ':trans_key' and locale "
                . "':locale'.";

        $replace = [
            'placeholder' => $placeholder,
            'trans_key' => $transKey,
            'locale' => $locale
        ];

        return __($errorMsg, $replace);
    }

    private function getReplaceFailMsg(
            string $replaceKey,
            string $transKey,
            ?string $locale = null
    ): string {
        $locale ??= App::currentLocale();

        $errorMsg = "The replace value of the key ':replace_key' was not found "
                . "in the resulting translation of ':trans_key' for locale "
                . "':locale'";

        $replace = ['replace_key' => $replaceKey, 'trans_key' => $transKey,
            'locale' => $locale];

        return __($errorMsg, $replace);
    }
}
