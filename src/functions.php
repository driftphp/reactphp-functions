<?php


namespace Mmoreram\React;

use React\ChildProcess\Process;
use React\EventLoop\LoopInterface;
use React\Promise\Deferred;
use React\Promise\PromiseInterface;
use React\Promise\Timer;

/**
 * Sleep for n seconds
 *
 * Generates a Promise that is properly resolved after this
 * time is elapsed
 *
 * @param int $time
 * @param LoopInterface $loop
 *
 * @return PromiseInterface
 */
function sleep(int $time, LoopInterface $loop) : PromiseInterface
{
    return Timer\resolve($time, $loop);
}

/**
 * Sleep for n microseconds
 *
 * Generates a Promise that is properly resolved after this
 * time is elapsed
 *
 * @param int $time
 * @param LoopInterface $loop
 *
 * @return PromiseInterface
 */
function usleep(int $time, LoopInterface $loop) : PromiseInterface
{
    return Timer\resolve($time/1000000, $loop);
}

/**
 * Returns the content type in MIME format, like text/plain or
 * application/octet-stream, or rejects with a \RuntimeException on failure
 *
 * @param string $fileName
 * @param LoopInterface $loop
 *
 * @return PromiseInterface
 */
function mime_content_type(string $fileName, LoopInterface $loop) : PromiseInterface
{
    $process = new Process(sprintf('php -d error_reporting=0 -r "echo mime_content_type(%s);"', escapeshellarg($fileName)));

    $process->start($loop);
    $deferred = new Deferred();
    $data = '';
    $stdout = $process->stdout;

    $stdout->on('data', function($chunk) use (&$data) {
        $data .= $chunk;
    });

    $stdout->on('end', function() use (&$data, $deferred, $fileName) {
        empty($data)
            ? $deferred->reject(new \RuntimeException(sprintf(
                "React\mime_content_type(%s): failed to open stream", $fileName
            )))
            : $deferred->resolve(trim($data));
    });

    return $deferred->promise();
}


