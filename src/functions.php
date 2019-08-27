<?php


namespace Mmoreram\React;

use React\EventLoop\LoopInterface;
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
