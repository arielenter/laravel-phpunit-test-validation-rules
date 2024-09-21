<?php

namespace Arielenter\ValidationAssertions\Tests\Unit;

use Arielenter\ValidationAssertions\Tests\TestCase;
use Illuminate\Filesystem\LocalFilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use function __;

class ReadmeTest extends TestCase {

    public string $testTransPrefix = 'arielenter_validation_assertions_test::';
    private LocalFilesystemAdapter $storage;

    public function getReadmeContent() {
        $replace = array_merge(__("{$this->testTransPrefix}readme"),
                __("{$this->testTransPrefix}files.readme_replace", [], 'en'));

        return __("{$this->testTransPrefix}files.readme_template", $replace,
                'en');
    }

    public function writeReadmeFile() {
        $base_path = __DIR__ . '/../../';
        $this->storage = Storage::createLocalDriver(['root' => $base_path]);
        $this->storage->put('README.md', $this->getReadmeContent());
    }

    public function test_readme_file() {
        $this->writeReadmeFile();

        $fileContent = $this->storage->get('README.md');

        $this->assertSame($this->getReadmeContent(), $fileContent);
    }
}
