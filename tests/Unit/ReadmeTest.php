<?php

namespace Arielenter\ValidationAssertions\Tests\Unit;

use Arielenter\ValidationAssertions\Tests\Support\TransAssertions;
use Arielenter\ValidationAssertions\Tests\TestCase;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use PHPUnit\Framework\Attributes\Test;
use function trans;

class ReadmeTest extends TestCase {

    use TransAssertions;

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
        $readmeFiles = $this->tryGetTrans("{$prefix}files.readme_replace",
                locale: 'en');
        $replace = array_merge($readmeSnips, $readmeFiles);

        $withoutComments = $this->tryGetTrans("{$prefix}files.readme_template",
                $replace, 'en');

        trans()->addLines(["readme.without_comments" => $withoutComments],
                App::getLocale());

        return $this->tryGetTrans("readme.without_comments",
                        $this->tryGetTrans("{$prefix}comments"));
    }
}
