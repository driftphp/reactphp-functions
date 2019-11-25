<?php


namespace Drift\React\Tests;

use PHPUnit\Framework\TestCase;
use React\EventLoop\Factory;
use React\Promise\FulfilledPromise;
use Drift\React;
use Clue\React\Block;

/**
 * Class UsleepTest
 */
class UsleepTest extends TestCase
{
    function testIt()
    {
        $loop = Factory::create();
        $elements = [];

        $promiseXZ = (new FulfilledPromise())
            ->then(function() use (&$elements){
                $elements[] = 'X';
            })
            ->then(function() use ($loop) {
                return React\usleep(200, $loop);
            })
            ->then(function() use (&$elements){
                $elements[] = 'Z';
            });

        $promiseY = (new FulfilledPromise())
            ->then(function() use ($loop) {
                return React\usleep(100, $loop);
            })
            ->then(function() use (&$elements) {
                $elements[] = 'Y';
            });

        Block\awaitAll([$promiseXZ, $promiseY], $loop);
        $this->assertEquals(['X', 'Y', 'Z'], $elements);
    }
}