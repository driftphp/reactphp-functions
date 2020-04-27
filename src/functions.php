<?php


namespace Drift\React;

use Evenement\EventEmitterInterface;
use React\ChildProcess\Process;
use React\EventLoop\LoopInterface;
use React\EventLoop\TimerInterface;
use React\Promise\Deferred;
use React\Promise\PromiseInterface;
use React\Promise\Timer;
use React\Stream\ReadableStreamInterface;
use function React\Promise\reject;

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

/**
 * Wait until a number of listeners are aware of stream data.
 *
 * @param EventEmitterInterface $stream
 * @param LoopInterface $loop
 * @param int $minimumListeners
 * @param int $timeout
 *
 * @return PromiseInterface<ReadableStreamInterface>
 */
function wait_for_stream_listeners(
    EventEmitterInterface $stream,
    LoopInterface $loop,
    int $minimumListeners = 1,
    float $timeout = -1
) : PromiseInterface
{
    if ($minimumListeners < 0) {
        return reject(new \LogicException('You cannot expect negative amount of listeners in a stream.'));
    }

    $deferred = new Deferred();
    $timer = $loop->addPeriodicTimer(0.001, function(TimerInterface $timer) use ($deferred, $stream, $minimumListeners, $loop) {
        if (count($stream->listeners('data')) >= $minimumListeners) {
            $loop->cancelTimer($timer);
            $deferred->resolve($stream);
        }
    });

    if ($timeout>0) {
        $loop->addTimer($timeout, function() use ($timer, $loop, $deferred, $timeout) {
            $loop->cancelTimer($timer);
            $deferred->reject(new \RuntimeException("No listeners attached after $timeout seconds"));
        });
    }

    return $deferred->promise();
}
