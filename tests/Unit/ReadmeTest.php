<?php

namespace Arielenter\ValidationAssertions\Tests\Unit;

use Arielenter\Validation\Constants\SupportedRequestMethods;
use Arielenter\ValidationAssertions\Tests\Support\TransAssertions;
use Arielenter\ValidationAssertions\Tests\TestCase;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use PHPUnit\Framework\Attributes\Test;
use function __;
use function trans;

class ReadmeTest extends TestCase {

    use TransAssertions,
        SupportedRequestMethods;

    public string $transPrefix = 'arielenter_validation_assertions_test::';

    #[Test]
    public function readme_files(): void {
        $this->makeReadmeFileAndTestIt('README', 'en');

        $this->makeReadmeFileAndTestIt('README.es', 'es');
    }

    public function makeReadmeFileAndTestIt(
            string $fileName,
            string $locale
    ): void {
        App::setLocale($locale);

        $this->writeReadmeFile($fileName);

        $fileContent = File::get("{$fileName}.md");

        $this->assertSame($this->getReadmeContent(), $fileContent);
    }

    public function writeReadmeFile(string $fileName): void {
        File::put("{$fileName}.md", $this->getReadmeContent());
    }

    public function getReadmeContent(): string {
        $prefix = $this->transPrefix;

        $readmeSnips = $this->tryGetTrans("{$prefix}readme");
        $readmeFiles = $this->getReadmeFiles();
        $replace = array_merge($readmeSnips, $readmeFiles);

        $replace['supported_methods'] = $this->getSupportedMethodsList();

        $withoutComments = __($this->getReadmeTemplate(), $replace);

        trans()->addLines(["readme.without_comments" => $withoutComments],
                App::getLocale());

        return $this->tryGetTrans("readme.without_comments",
                        $this->tryGetTrans("{$prefix}comments"));
    }

    public function getSupportedMethodsList() {
        $methodsList = '';
        foreach ($this::SUPPORTED_METHODS as $method) {
            $methodsList .= "+ {$method}\n";
        }
        return $methodsList;
    }

    public function getReadmeTemplate() {
        return $readmeTemplate = File::get('resources/readme_template.md');
    }

    public function getReadmeFiles() {
        $filesPath = [
            'routes' => 'routes/web.php',
            'tests' => 'tests/Feature/RoutesValidationTest.php',
            'code' => 'tests/Feature/AssertionsCodeInANutshellTest.php'
        ];

        $filesNameAndContent = [];

        foreach ($filesPath as $key => $filePath) {
            $filesNameAndContent[$key . '_file'] = basename($filePath);
            $filesNameAndContent[$key . '_file_content'] = str_replace(
                    'Arielenter\ValidationAssertions\\', '',
                    File::get($filePath));
        }

        return $filesNameAndContent;
    }
}
