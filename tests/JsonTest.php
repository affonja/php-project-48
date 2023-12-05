<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\genDiff;
use function Differ\parseFile;

class JsonTest extends TestCase
{
    public function testGenDiffValidFile(): void
    {
        $path1 = 'tests/fixtures/file1.json';
        $path2 = 'tests/fixtures/file2.json';

        $expected = "- follow: false" . PHP_EOL .
            "  host: hexlet.io" . PHP_EOL .
            "- proxy: 123.234.53.22" . PHP_EOL .
            "- timeout: 50" . PHP_EOL .
            "+ timeout: 20" . PHP_EOL .
            "+ verbose: true";

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

        $this->assertEquals($expected, parseFile('fixture/file1.json'));
        $this->assertEquals($expected, parseFile('fixture/file1.yaml'));
    }

//    public function testUnValidExt(): void
//    {
//        $this->expectException(\Exception::class);
//        $this->expectExceptionMessage('Unknown extension file');
//
//        $path = 'tests/fixtures/file1000';
//
//        parseFile($path);
//    }
}
