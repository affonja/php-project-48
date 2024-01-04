<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;
use Differ;

use function Differ\genDiff;
use function Differ\getFullPath;
use function Differ\parseFile;

const FIXTURES_DIR = '/fixtures/';

class DiffTest extends TestCase
{
    public function testGenDiffValidFile(): void
    {
        $path1 = 'tests/fixtures/plain1.json';
        $path2 = 'tests/fixtures/plain2.json';

        $expected = "{" . PHP_EOL .
            "  - follow: false" . PHP_EOL .
            "    host: hexlet.io" . PHP_EOL .
            "  - proxy: 123.234.53.22" . PHP_EOL .
            "  - timeout: 50" . PHP_EOL .
            "  + timeout: 20" . PHP_EOL .
            "  + verbose: true" . PHP_EOL . "}";

        $result = genDiff($path1, $path2);

        $this->assertEquals($expected, $result);
    }

    public function testGenDiffUnValidFile(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('File not exist');

        $path1 = 'tests/fixtures/file1000.json';
        $path2 = 'tests/fixtures/file2.json';

        genDiff($path1, $path2);
    }

    public function testValidExt(): void
    {
        $expected = [
            "host" => "hexlet.io",
            "timeout" => 50,
            "proxy" => '123.234.53.22',
            "follow" => false
        ];

        $this->assertEquals($expected, parseFile('tests/fixtures/plain1.json'));
        $this->assertEquals($expected, parseFile('tests/fixtures/plain1.yaml'));
    }

    public function testUnValidExt(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Unknown extension file');

        $path = 'tests/fixtures/file1000';

        parseFile($path);
    }

    /*********filepath**********/
    public function testGetFullPath()
    {
        // Тест для относительного пути
        $relativePath = getFullPath('plain1.json');
        $expectedPath = __DIR__ . '/fixtures/plain1.json';
        $this->assertEquals(realpath($expectedPath), $relativePath);
    }

    public function testGetTypePath()
    {
        // Тест для абсолютного пути
        $isAbsolutePath = Differ\getTypePath('/fixtures/plain1.json');
        $this->assertTrue($isAbsolutePath);

        // Тест для относительного пути
        $isRelativePath = Differ\getTypePath('test.txt');
        $this->assertFalse($isRelativePath);
    }

    public function testGetExtension()
    {
        // Тест для файла с расширением
        $fileWithExtension = Differ\getExtension('plain1.json');
        $this->assertEquals('json', $fileWithExtension);

        // Тест для файла без расширения
        $fileWithoutExtension = Differ\getExtension('plain1');
        $this->assertEquals('', $fileWithoutExtension);
    }

    /*********parcer**********/

    public function testParseJsonFile()
    {
        $jsonPath = FIXTURES_DIR . 'plain1.json';
        $expectedArray = [
            "host" => "hexlet.io",
            "timeout" => 50,
            "proxy" => "123.234.53.22",
            "follow" => false
        ];

        $parsedData = Differ\parseFile($jsonPath);

        $this->assertEquals($expectedArray, $parsedData);
    }

    public function testParseYamlFile()
    {
        $yamlPath = FIXTURES_DIR . 'plain1.yaml';
        $expectedArray = [
            "host" => "hexlet.io",
            "timeout" => 50,
            "proxy" => "123.234.53.22",
            "follow" => false
        ];

        $parsedData = Differ\parseFile($yamlPath);

        $this->assertEquals($expectedArray, $parsedData);
    }

    public function testUnknownExtensionFile()
    {
        $this->expectException(\Exception::class);

        $unknownPath = FIXTURES_DIR . 'example.txt';

        Differ\parseFile($unknownPath);
    }

    /*********stylish**********/
    public function testFormatterWithScalarValues()
    {
        $fileContents = file_get_contents('tests/fixtures/diffs');
        $nestedArr = explode("\n\n\n", trim($fileContents));
        $diff = json_decode($nestedArr[0], true);

        $fileContents = file_get_contents('tests/fixtures/expected');
        $nestedArr = explode("\n\n\n", trim($fileContents));
        $expectedResult = trim($nestedArr[0]);

        $formattedResult = trim(Differ\formatter($diff));

        $this->assertEquals($expectedResult, $formattedResult);
    }

    public function testFormatterWithNestedArray()
    {
        $fileContents = file_get_contents('tests/fixtures/diffs');
        $nestedArr = explode("\n\n\n", trim($fileContents));
        $diff = json_decode($nestedArr[1], true);

        $fileContents = file_get_contents('tests/fixtures/expected');
        $nestedArr = explode("\n\n\n", trim($fileContents));
        $expectedResult = trim($nestedArr[1]);

        $formattedResult = trim(Differ\formatter($diff));

        $this->assertEquals($expectedResult, $formattedResult);
    }
}
