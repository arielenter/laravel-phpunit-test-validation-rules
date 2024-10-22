<?php

namespace Arielenter\Validation\Constants;

trait SupportedRequestMethods {

    const SUPPORTED_METHODS = ['get', 'post', 'put', 'patch', 'delete', 
        'options', 'getJson', 'postJson', 'putJson', 'patchJson', 'deleteJson', 
        'optionsJson'];
}
