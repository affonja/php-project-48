<?php

namespace Differ\Tests;

use Exception;
use PHPUnit\Framework\TestCase;
use Differ;

use function Differ\genDiff;
use function Differ\getFullPath;

const FIXTURES_DIR = __DIR__ . '/fixtures/';

class DiffTest extends TestCase
{
    public function testParseJsonFile()
    {
        $fileContents = file_get_contents(FIXTURES_DIR . 'expected');
        $expectedJSON = explode("\n\n\n", $fileContents)[0];
        $expected = json_decode($expectedJSON, true);
        $jsonPath = FIXTURES_DIR . 'plain1.json';

        $parsedData = Differ\parseFile($jsonPath);

        $this->assertEquals($expected, $parsedData);
    }

    public function testParseYamlFile()
    {
        $fileContents = file_get_contents(FIXTURES_DIR . 'expected');
        $expectedJSON = explode("\n\n\n", $fileContents)[0];
        $expected = json_decode($expectedJSON, true);
        $yamlPath = FIXTURES_DIR . 'plain1.yaml';

        $parsedData = Differ\parseFile($yamlPath);

        $this->assertEquals($expected, $parsedData);
    }

    public function testUnknownExtensionFile()
    {
        $this->expectException(Exception::class);

        $unknownPath = FIXTURES_DIR . 'example.txt';

        Differ\parseFile($unknownPath);
    }

    public function testUnknownFormatter()
    {
        $this->expectException(Exception::class);

        $nameFormatter = 'unknown';

        Differ\getFormatter($nameFormatter, []);
    }

    public function testStylishPlain()
    {
        $fileContents = file_get_contents(FIXTURES_DIR . 'expected');
        $arr = explode("\n\n\n", trim($fileContents));
        $expected = trim($arr[1]);

        $fileContents = file_get_contents(FIXTURES_DIR . 'diffs');
        $arr = explode("\n\n\n", trim($fileContents));
        $diff = json_decode($arr[0], true);
        $formattedResult = trim(Differ\stylish($diff));


        $this->assertEquals($expected, $formattedResult);
    }

    public function testStylishNested()
    {
        $fileContents = file_get_contents(FIXTURES_DIR . 'expected');
        $arr = explode("\n\n\n", trim($fileContents));
        $expected = trim($arr[2]);

        $fileContents = file_get_contents(FIXTURES_DIR . 'diffs');
        $arr = explode("\n\n\n", trim($fileContents));
        $diff = json_decode($arr[1], true);
        $formattedResult = trim(Differ\stylish($diff));

        $this->assertEquals($expected, $formattedResult);
    }

    public function testPlain(): void
    {
        $fileContents = file_get_contents(FIXTURES_DIR . 'expected');
        $arr = explode("\n\n\n", trim($fileContents));
        $expected = trim($arr[4]);

        $fileContents = file_get_contents(FIXTURES_DIR . 'diffs');
        $arr = explode("\n\n\n", trim($fileContents));
        $diff = json_decode($arr[1], true);
        $formattedResult = trim(Differ\plain($diff));

        $this->assertEquals($expected, $formattedResult);
    }

    public function testJSON(): void
    {
        $fileContents = file_get_contents(FIXTURES_DIR . 'expected');
        $arr = explode("\n\n\n", trim($fileContents));
        $expected = trim($arr[6]);

        $fileContents = file_get_contents(FIXTURES_DIR . 'diffs');
        $arr = explode("\n\n\n", trim($fileContents));
        $diff = json_decode($arr[1], true);
        $formattedResult = trim(Differ\json($diff));

        $this->assertEquals($expected, $formattedResult);
    }

    public function testGenDiffValidFile(): void
    {
        $fileContents = file_get_contents(FIXTURES_DIR . 'expected');
        $arr = explode("\n\n\n", trim($fileContents));
        $expected = trim($arr[3]);

        $path1 = FIXTURES_DIR . 'plain1.json';
        $path2 = FIXTURES_DIR . 'plain2.json';

        $result = genDiff($path1, $path2);

        $this->assertEquals($expected, $result);
    }

    public function testGenDiffUnValidFile(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('File not exist');

        $path1 = 'tests/fixtures/file1000.json';
        $path2 = 'tests/fixtures/file2.json';

        genDiff($path1, $path2);
    }

    public function testGenDiffNestedFile(): void
    {
        $fileContents = file_get_contents(FIXTURES_DIR . 'expected');
        $arr = explode("\n\n\n", trim($fileContents));
        $expected = trim($arr[7]);

        $path1 = FIXTURES_DIR . 'nest1.json';
        $path2 = FIXTURES_DIR . 'nest2.json';

        $result = genDiff($path1, $path2);

        $this->assertEquals($expected, $result);
    }
}
