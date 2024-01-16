<?php

namespace Differ\Tests;

use Exception;
use PHPUnit\Framework\TestCase;
use Differ\Differ;

use function Differ\Differ\genDiff;
use function Differ\Differ\getFullPath;

const FIXTURES_DIR = __DIR__ . '/fixtures/';

class DiffTest extends TestCase
{
    public function testGetFullPath()
    {
        $paths = [
            'plain1.json',
            'tests/fixtures/plain1.json',
            './tests/fixtures/plain1.json',
            '/tests/fixtures/plain1.json',
            './plain1.json'
        ];
        $expectedPath = FIXTURES_DIR . 'plain1.json';
        foreach ($paths as $path) {
            $this->assertEquals(realpath($expectedPath), getFullPath($path));
        }
    }

    public function testGetTypePath()
    {
        $isAbsolutePath = Differ\getTypePath('/fixtures/plain1.json');
        $this->assertTrue($isAbsolutePath);

        $isRelativePath = Differ\getTypePath('test.txt');
        $this->assertFalse($isRelativePath);
    }

    public function testGetExtension()
    {
        $fileWithExtension = Differ\getExtension('plain1.json');
        $this->assertEquals('json', $fileWithExtension);

        $fileWithExtension = Differ\getExtension('plain1.yaml');
        $this->assertEquals('yaml', $fileWithExtension);

        $fileWithoutExtension = Differ\getExtension('plain1');
        $this->assertEquals('', $fileWithoutExtension);
    }

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

        $unknownPath = FIXTURES_DIR . 'diffs';

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
        $expected = $arr[1];

        $fileContents = file_get_contents(FIXTURES_DIR . 'diffs');
        $arr = explode("\n\n\n", trim($fileContents));
        $diff = json_decode($arr[0], true);
        $formattedResult = rtrim(implode('', Differ\stylish($diff)));


        $this->assertEquals($expected, $formattedResult);
    }

    public function testStylishNested()
    {
        $fileContents = file_get_contents(FIXTURES_DIR . 'expected');
        $arr = explode("\n\n\n", trim($fileContents));
        $expected = $arr[2];

        $fileContents = file_get_contents(FIXTURES_DIR . 'diffs');
        $arr = explode("\n\n\n", trim($fileContents));
        $diff = json_decode($arr[1], true);
        $formattedResult = rtrim(implode('', Differ\stylish($diff)));

        $this->assertEquals($expected, $formattedResult);
    }

    public function testPlain(): void
    {
        $fileContents = file_get_contents(FIXTURES_DIR . 'expected');
        $arr = explode("\n\n\n", trim($fileContents));
        $expected = $arr[4];

        $fileContents = file_get_contents(FIXTURES_DIR . 'diffs');
        $arr = explode("\n\n\n", trim($fileContents));
        $diff = json_decode($arr[1], true);
        $formattedResult = trim(implode('', Differ\plain($diff)));

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
        $formattedResult = implode('', Differ\json($diff));

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

        $path1 = FIXTURES_DIR . 'plain1000.json';
        $path2 = FIXTURES_DIR . 'plain1.json';

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
