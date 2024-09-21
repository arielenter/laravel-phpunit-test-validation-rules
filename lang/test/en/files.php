<?php

use Illuminate\Support\Facades\Storage;

$storage = Storage::createLocalDriver(['root' => __DIR__ . '/../../../']);

$readme_template = $storage->get('resources/readme_template.md');

$files = [
    'routes' => 'routes/web.php',
    'tests' => 'tests/Feature/RoutesValidationTest.php',
    'code' => 'tests/Feature/AssertionsCodeInANutshellTest.php'
];

$filesNamesAndContent = [];
foreach ($files as $key => $file) {
    $filesNamesAndContent[$key . '_file'] = basename($file);
    $filesNamesAndContent[$key . '_file_content'] = $storage->get($file);
}

return [
    'readme_template' => $readme_template,
    'readme_replace' => $filesNamesAndContent
];
