<?php


namespace Mmoreram\React\Tests;

use PHPUnit\Framework\TestCase;
use React\EventLoop\Factory;
use React\Promise\FulfilledPromise;
use Mmoreram\React;
use Clue\React\Block;

/**
 * Class SleepTest
 */
class SleepTest extends TestCase
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
                return React\sleep(2, $loop);
            })
            ->then(function() use (&$elements){
                $elements[] = 'Z';
            });

        $promiseY = (new FulfilledPromise())
            ->then(function() use ($loop) {
                return React\sleep(1, $loop);
            })
            ->then(function() use (&$elements) {
                $elements[] = 'Y';
            });

        Block\awaitAll([$promiseXZ, $promiseY], $loop);
        $this->assertEquals(['X', 'Y', 'Z'], $elements);
    }
}