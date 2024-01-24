<?php

namespace Differ\Tests;

use Exception;
use PHPUnit\Framework\TestCase;
use Differ\Differ;

use function Differ\Differ\genDiff;
use function Differ\Differ\getFullPath;

const FIXTURES_DIR = __DIR__ . '/fixtures/';
define("EXPECTED_JSON", explode("\n\n\n", file_get_contents(FIXTURES_DIR . 'expected')));
define("DIFF", explode("\n\n\n", file_get_contents(FIXTURES_DIR . 'diffs')));


class DiffTest extends TestCase
{
    public static function pathProvider(): array
    {
        return [
            ['plain1.json', realpath(FIXTURES_DIR . 'plain1.json')],
            ['tests/fixtures/plain1.json', realpath(FIXTURES_DIR . 'plain1.json')],
            ['./tests/fixtures/plain1.json', realpath(FIXTURES_DIR . 'plain1.json')],
            ['/tests/fixtures/plain1.json', realpath(FIXTURES_DIR . 'plain1.json')],
            ['./plain1.json', realpath(FIXTURES_DIR . 'plain1.json')],
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

    public function testGetTypePath()
    {
        $isAbsolutePath = Differ\isAbsolutePath('/fixtures/plain1.json');
        $this->assertTrue($isAbsolutePath);

        $isRelativePath = Differ\isAbsolutePath('test.txt');
        $this->assertFalse($isRelativePath);
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
        $extension = Differ\getExtension($input);
        $this->assertEquals($expected, $extension);
    }

    public static function parserProvider(): array
    {
        return [
            [FIXTURES_DIR . 'plain1.json', json_decode(EXPECTED_JSON[0], true)],
            [FIXTURES_DIR . 'plain1.yaml', json_decode(EXPECTED_JSON[0], true)],
        ];
    }

    /**
     * @dataProvider parserProvider
     */
    public function testParseValidFile($path, $expected)
    {
        $parsedData = Differ\parseFile($path);

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

    public static function formatterProvider(): array
    {
        return [
            [json_decode(DIFF[0], true), 'stylish', EXPECTED_JSON[1]],
            [json_decode(DIFF[0], true), 'plain', EXPECTED_JSON[2]],
            [json_decode(DIFF[0], true), 'json', EXPECTED_JSON[3]],
            [json_decode(DIFF[1], true), 'stylish', EXPECTED_JSON[6]],
            [json_decode(DIFF[1], true), 'plain', EXPECTED_JSON[4]],
            [json_decode(DIFF[1], true), 'json', EXPECTED_JSON[5]],
        ];
    }

    /**
     * @dataProvider formatterProvider
     */
    public function testFormatters($input, $format_name, $expected)
    {
        $result = Differ\getFormatter($format_name, $input);
        $this->assertEquals($expected, $result);
    }

    public static function genDiffProvider(): array
    {
        return [
            [FIXTURES_DIR . 'plain1.json', FIXTURES_DIR . 'plain2.json', 'stylish', EXPECTED_JSON[1]],
            [FIXTURES_DIR . 'nest1.json', FIXTURES_DIR . 'nest2.json', 'stylish', EXPECTED_JSON[6]],
            [FIXTURES_DIR . 'nest1.json', FIXTURES_DIR . 'nest2.json', 'plain', EXPECTED_JSON[4]],
            [FIXTURES_DIR . 'nest1.json', FIXTURES_DIR . 'nest2.json', 'json', EXPECTED_JSON[5]],
            [FIXTURES_DIR . 'nest1.yaml', FIXTURES_DIR . 'nest2.yml', 'stylish', EXPECTED_JSON[6]],
            [FIXTURES_DIR . 'nest1.yaml', FIXTURES_DIR . 'nest2.yml', 'plain', EXPECTED_JSON[4]],
            [FIXTURES_DIR . 'nest1.yaml', FIXTURES_DIR . 'nest2.yml', 'json', EXPECTED_JSON[5]],
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
