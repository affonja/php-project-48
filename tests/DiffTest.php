<?php

namespace Differ\Tests;

use Exception;
use PHPUnit\Framework\TestCase;

use function Differ\Formatters\getFormatter;
use function Differ\Differ\genDiff;
use function Differ\FilePath\getExtension;
use function Differ\FilePath\getFullPath;
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
            [FIXTURES_DIR . 'plain1.json', $json],
            [FIXTURES_DIR . 'plain1.yaml', $json],
        ];
    }

    /**
     * @dataProvider parserProvider
     */
    public function testParseValidFile($path, $expected)
    {
        $parsedData = parseFile($path);

        $this->assertEquals($expected, $parsedData);
    }

    public function testUnknownExtensionFile()
    {
        $this->expectException(Exception::class);

        $unknownPath = FIXTURES_DIR . '/testData/diff-plain';

        parseFile($unknownPath);
    }

    public function testUnknownFormatter()
    {
        $this->expectException(Exception::class);

        $nameFormatter = 'unknown';

        getFormatter($nameFormatter, []);
    }

    public static function formatterProvider(): array
    {
        $diffPlain = json_decode(file_get_contents(FIXTURES_DIR . '/testData/diff-plain'), true);
        $diffNested = json_decode(file_get_contents(FIXTURES_DIR . '/testData/diff-nested'), true);

        $expext1 = file_get_contents(FIXTURES_DIR . '/testData/expect-stylish-plain');
        $expext2 = file_get_contents(FIXTURES_DIR . '/testData/expect-plain-plain');
        $expext3 = file_get_contents(FIXTURES_DIR . '/testData/expect-json-plain');
        $expext4 = file_get_contents(FIXTURES_DIR . '/testData/expect-stylish-nested');
        $expext5 = file_get_contents(FIXTURES_DIR . '/testData/expect-plain-nested');
        $expext6 = file_get_contents(FIXTURES_DIR . '/testData/expect-json-nested');

        return [
            [$diffPlain, 'stylish', $expext1],
            [$diffPlain, 'plain', $expext2],
            [$diffPlain, 'json', $expext3],
            [$diffNested, 'stylish', $expext4],
            [$diffNested, 'plain', $expext5],
            [$diffNested, 'json', $expext6],
        ];
    }

    /**
     * @dataProvider formatterProvider
     */
    public function testFormatters($input, $format_name, $expected)
    {
        $result = getFormatter($format_name, $input);
        $this->assertEquals($expected, $result);
    }

    public static function genDiffProvider(): array
    {
        $expext1 = file_get_contents(FIXTURES_DIR . '/testData/expect-stylish-plain');
        $expext2 = file_get_contents(FIXTURES_DIR . '/testData/expect-stylish-nested');
        $expext3 = file_get_contents(FIXTURES_DIR . '/testData/expect-plain-nested');
        $expext4 = file_get_contents(FIXTURES_DIR . '/testData/expect-json-nested');
        $expext5 = file_get_contents(FIXTURES_DIR . '/testData/expect-stylish-nested');
        $expext6 = file_get_contents(FIXTURES_DIR . '/testData/expect-plain-nested');
        $expext7 = file_get_contents(FIXTURES_DIR . '/testData/expect-json-nested');

        return [
            [FIXTURES_DIR . 'plain1.json', FIXTURES_DIR . 'plain2.json', 'stylish', $expext1],
            [FIXTURES_DIR . 'nest1.json', FIXTURES_DIR . 'nest2.json', 'stylish', $expext2],
            [FIXTURES_DIR . 'nest1.json', FIXTURES_DIR . 'nest2.json', 'plain', $expext3],
            [FIXTURES_DIR . 'nest1.json', FIXTURES_DIR . 'nest2.json', 'json', $expext4],
            [FIXTURES_DIR . 'nest1.yaml', FIXTURES_DIR . 'nest2.yml', 'stylish', $expext5],
            [FIXTURES_DIR . 'nest1.yaml', FIXTURES_DIR . 'nest2.yml', 'plain', $expext6],
            [FIXTURES_DIR . 'nest1.yaml', FIXTURES_DIR . 'nest2.yml', 'json', $expext7],
        ];
    }

    /**
     * @dataProvider genDiffProvider
     */
    public function testGenDiffValidFile($path1, $path2, $format_name, $expected): void
    {
        $result = genDiff($path1, $path2, $format_name);
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
}
