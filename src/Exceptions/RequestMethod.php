<?php

namespace Arielenter\Validation\Exceptions;

use Arielenter\Validation\Constants\SupportedRequestMethods;
use Arielenter\Validation\Constants\AssertionsTrans;
use Illuminate\Support\Str;
use ValueError;
use function __;

class RequestMethod extends ValueError {

    use AssertionsTrans,
        SupportedRequestMethods;

    public function __construct(string $requestMethod) {
        $message = __(
                $this::ASSERTIONS_ERRORS_TRANS . 'unsupported_request_method',
                [
                    'method' => $requestMethod,
                    'supported_methods' => json_encode($this::SUPPORTED_METHODS)
                ]
        );

        return parent::__construct($message);
    }

    public static function validate(string $requestMethod)
    : string {
        $method = Str::of($requestMethod)->camel()->lower()->replace('j', 'J')
                ->toString();

        if (in_array($method, self::SUPPORTED_METHODS)) {
            return $method;
        }

        throw new self($requestMethod);
    }
}
