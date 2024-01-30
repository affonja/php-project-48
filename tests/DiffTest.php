<?php

namespace Differ\Tests;

use Exception;
use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;
use function Differ\Differ\getParsedData;
use function Differ\FilePath\getExtension;
use function Differ\FilePath\getFullPath;
use function Differ\Formatters\Formatters\getFormatter;
use function Differ\Parsers\parseFile;

const FIXTURES_DIR = __DIR__ . '/fixtures/';

class DiffTest extends TestCase
{
    public static function pathProvider(): array
    {
        return [
            ['tests/fixtures/plain1.json', realpath(FIXTURES_DIR . 'plain1.json')],
            ['./tests/fixtures/plain1.json', realpath(FIXTURES_DIR . 'plain1.json')],
            ['C:/tests/plain1.json', false],
            ['/home/plain1.json', false],
        ];
    }

    /**
     * @dataProvider pathProvider
     */
    public function testGetFullPath($input, $expected)
    {
        $result = getFullPath($input);
        $this->assertEquals($expected, $result);
    }

    public static function extensionProvider(): array
    {
        return [
            ['plain1.json', 'json'],
            ['plain1.yaml', 'yaml'],
            ['plain1', ''],
        ];
    }

    /**
     * @dataProvider extensionProvider
     */
    public function testGetExtension($input, $expected)
    {
        $extension = getExtension($input);
        $this->assertEquals($expected, $extension);
    }

    public static function parserProvider(): array
    {
        $json = json_decode(file_get_contents(FIXTURES_DIR . '/testData/expect-file1'), true);
        return [
            [file_get_contents(FIXTURES_DIR . 'plain1.json'), 'json', $json],
            [file_get_contents(FIXTURES_DIR . 'plain1.yaml'), 'yaml', $json],
        ];
    }

    /**
     * @dataProvider parserProvider
     */
    public function testParseValidFile($content, $type, $expected)
    {
        $parsedData = parseFile($content, $type);

        $this->assertEquals($expected, $parsedData);
    }

    public function testUnknownExtensionFile()
    {
        $this->expectException(Exception::class);

        $unknownPath = FIXTURES_DIR . '/testData/diff-plain';

        getParsedData($unknownPath);
    }

    public function testUnknownFormatter()
    {
        $this->expectException(Exception::class);

        $nameFormatter = 'unknown';

        getFormatter($nameFormatter, []);
    }

    public static function formatterProvider(): array
    {
        return [
            ['/testData/diff-plain', 'stylish', '/testData/expect-stylish-plain'],
            ['/testData/diff-plain', 'plain', '/testData/expect-plain-plain'],
            ['/testData/diff-plain', 'json', '/testData/expect-json-plain'],
            ['/testData/diff-nested', 'stylish', '/testData/expect-stylish-nested'],
            ['/testData/diff-nested', 'plain', '/testData/expect-plain-nested'],
            ['/testData/diff-nested', 'json', '/testData/expect-json-nested'],
        ];
    }

    /**
     * @dataProvider formatterProvider
     */
    public function testFormatters($path_to_input, $format_name, $path_to_expected)
    {
        $input = json_decode(file_get_contents(FIXTURES_DIR . $path_to_input), true);
        $actual = getFormatter($format_name, $input);
        $this->assertStringEqualsFile(FIXTURES_DIR . $path_to_expected, $actual);
    }

    public static function genDiffProvider(): array
    {
        return [
            ['plain1.json', 'plain2.json', 'stylish', '/testData/expect-stylish-plain'],
            ['nest1.json', 'nest2.json', 'stylish', '/testData/expect-stylish-nested'],
            ['nest1.json', 'nest2.json', 'plain', '/testData/expect-plain-nested'],
            ['nest1.json', 'nest2.json', 'json', '/testData/expect-json-nested'],
            ['nest1.yaml', 'nest2.yml', 'stylish', '/testData/expect-stylish-nested'],
            ['nest1.yaml', 'nest2.yml', 'plain', '/testData/expect-plain-nested'],
            ['nest1.yaml', 'nest2.yml', 'json', '/testData/expect-json-nested'],
        ];
    }

    /**
     * @dataProvider genDiffProvider
     */
    public function testGenDiffValidFile($path1, $path2, $format_name, $path_to_expected): void
    {
        $actual = genDiff(FIXTURES_DIR . $path1, FIXTURES_DIR . $path2, $format_name);
        $this->assertStringEqualsFile(FIXTURES_DIR . $path_to_expected, $actual);
    }

    public function testGenDiffUnValidFile(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('File not exist');

        $path1 = FIXTURES_DIR . 'plain1000.json';
        $path2 = FIXTURES_DIR . 'plain1.json';

        genDiff($path1, $path2);
    }
}
