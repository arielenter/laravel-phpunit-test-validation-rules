<?php

namespace Arielenter\Validation\Exceptions;

use ArgumentCountError;
use Arielenter\Validation\Constants\TransPrefix;
use function __;
use function json_encode;

class RowHasRequiredKeys extends ArgumentCountError {

    use TransPrefix;

    public function __construct(
            mixed $currentRow,
            string|int $currentRowKey,
            int $key
    ) {
        $message = __(
                $this::TRANS_PREFIX . 'row_has_a_missing_key',
                [
                    'row_key' => $currentRowKey,
                    'value' => json_encode($currentRow),
                    'missing_key' => $key
                ]
        );

        return parent::__construct($message);
    }

    public static function validate(
            mixed $currentRow,
            string|int $currentRowKey
    ) {
        for ($key = 0; $key <= 2; $key++) {
            if (array_key_exists($key, $currentRow)) {
                continue;
            }

            throw new self($currentRow, $currentRowKey, $key);
        }
    }
}
