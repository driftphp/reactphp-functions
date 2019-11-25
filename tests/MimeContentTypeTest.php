<?php


namespace Drift\React\Tests;

use PHPUnit\Framework\TestCase;
use React\EventLoop\Factory;
use Drift\React;
use Clue\React\Block;
use RuntimeException;
use Exception;

/**
 * Class MimeContentTypeTest
 */
class MimeContentTypeTest extends TestCase
{
    function testItResolvesWithMimeType()
    {
        $loop = Factory::create();
        $promises = [];
        $promises[] = React\mime_content_type(__DIR__ . '/static/file1.txt', $loop);
        $promises[] = React\mime_content_type(__DIR__ . '/static/pixel.png', $loop);

        $results = Block\awaitAll($promises, $loop);
        $this->assertEquals('text/plain', $results[0]);
        $this->assertEquals('image/png', $results[1]);
    }

    /**
     * @dataProvider getFilesWhereShouldReject
     *
     * @param string $filePath
     *
     * @throws Exception
     */
    function testIfRejectsOnFailure(string $filePath)
    {
        $this->expectException(RuntimeException::class);
        $loop = Factory::create();
        Block\await(React\mime_content_type($filePath, $loop), $loop);
    }

    /**
     * Get files where should reject
     */
    public function getFilesWhereShouldReject()
    {
        return [
            ['nonexisting.txt'],
            ["x'); echo('That\'s really bad'); echo ('x"]
        ];
    }
}