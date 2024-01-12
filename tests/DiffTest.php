<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;
use Differ;
use Exception;

use function Differ\genDiff;
use function Differ\getFullPath;
use function Differ\parseFile;

const FIXTURES_DIR = __DIR__ . '/fixtures/';

class DiffTest extends TestCase
{
    /*********filepath**********/
    public function testGetFullPath()
    {
        $paths = [
            'plain1.json',
            'tests/fixtures/plain1.json',
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
        // Тест для абсолютного пути
        $isAbsolutePath = Differ\getTypePath('/fixtures/plain1.json');
        $this->assertTrue($isAbsolutePath);

        // Тест для относительного пути
        $isRelativePath = Differ\getTypePath('test.txt');
        $this->assertFalse($isRelativePath);
    }

    public function testGetExtension()
    {
        // Тест для файла с расширением json
        $fileWithExtension = Differ\getExtension('plain1.json');
        $this->assertEquals('json', $fileWithExtension);

        // Тест для файла с расширением json
        $fileWithExtension = Differ\getExtension('plain1.yaml');
        $this->assertEquals('yaml', $fileWithExtension);

        // Тест для файла без расширения
        $fileWithoutExtension = Differ\getExtension('plain1');
        $this->assertEquals('', $fileWithoutExtension);
    }

    /*********parcer**********/
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
        $this->expectException(\Exception::class);

        $unknownPath = FIXTURES_DIR . 'example.txt';

        Differ\parseFile($unknownPath);
    }

    /*********stylish**********/
    public function testFormatterPlain()
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

    public function testFormatterNested()
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

    /*********stylish**********/
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

    /*********json**********/
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

    /*********diff**********/
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
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('File not exist');

        $path1 = 'tests/fixtures/file1000.json';
        $path2 = 'tests/fixtures/file2.json';

        genDiff($path1, $path2);
    }
}
