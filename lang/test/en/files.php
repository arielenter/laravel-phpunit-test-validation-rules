<?php

use Illuminate\Support\Facades\File;

$readmeTemplate = File::get('resources/readme_template.md');

$filesPath = [
    'routes' => 'routes/web.php',
    'tests' => 'tests/Feature/RoutesValidationTest.php',
    'code' => 'tests/Feature/AssertionsCodeInANutshellTest.php'
];

$filesNameAndContent = [];

foreach ($filesPath as $key => $filePath) {
    $filesNameAndContent[$key . '_file'] = basename($filePath);
    $filesNameAndContent[$key . '_file_content'] = str_replace('Arielenter'
            . '\ValidationAssertions\\', '', File::get($filePath));
}

return [
    'readme_template' => $readmeTemplate,
    'readme_replace' => $filesNameAndContent
];
