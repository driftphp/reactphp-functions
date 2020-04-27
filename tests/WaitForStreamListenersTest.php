<?php


namespace Drift\React\Tests;

use PHPUnit\Framework\TestCase;
use React\EventLoop\Factory;
use React\EventLoop\TimerInterface;
use React\Stream\ThroughStream;
use function Clue\React\Block\await;
use function Drift\React\wait_for_stream_listeners;

/**
 * Class WaitForStreamListenersTest
 */
class WaitForStreamListenersTest extends TestCase
{
    /**
     * Test without listeners
     */
    public function testNoListeners()
    {
        $this->expectNotToPerformAssertions();
        $loop = Factory::create();
        $stream = new ThroughStream();
        await(wait_for_stream_listeners($stream, $loop, 0), $loop);
    }

    /**
     * Test without listeners
     */
    public function testNegativeListeners()
    {
        $this->expectException(\LogicException::class);
        $loop = Factory::create();
        $stream = new ThroughStream();
        await(wait_for_stream_listeners($stream, $loop, -1), $loop);
    }

    /**
     * Test timeout
     */
    public function testTimeout()
    {
        $loop = Factory::create();
        $stream = new ThroughStream();
        try {
            await(wait_for_stream_listeners($stream, $loop, 1, 1), $loop);
            $this->fail('Timeout should reject');
        } catch (\Exception $exception) {
            $this->assertTrue(true);
        }
    }

    /**
     * Test one listener
     */
    public function testOneListener()
    {
        $this->expectNotToPerformAssertions();
        $loop = Factory::create();
        $stream = new ThroughStream();
        $stream->on('data', function(){});
        await(wait_for_stream_listeners($stream, $loop, 1, 1), $loop);
    }

    /**
     * Test two listener
     */
    public function testTwoListeners()
    {
        $loop = Factory::create();
        $stream = new ThroughStream();
        $stream->on('data', function(){});

        $from = time();
        try {
            await(wait_for_stream_listeners($stream, $loop, 2, 1), $loop);
            $this->fail('Timeout should reject');
        } catch (\Exception $exception) {
            $to = time();
            $this->assertTrue(intval($to-$from) == 1);
        }

        $stream->on('data', function(){});
        await(wait_for_stream_listeners($stream, $loop, 2, 1), $loop);
    }

    /**
     * Test future listener
     */
    public function testFutureListener()
    {
        $this->expectNotToPerformAssertions();
        $loop = Factory::create();
        $stream = new ThroughStream();

        $loop->addPeriodicTimer(0.1, function(TimerInterface $timer) use ($stream) {
            $stream->on('data', function() {});
        });

        await(wait_for_stream_listeners($stream, $loop, 10, 1.01), $loop);
    }

    /**
     * Test future listener
     *
     * @group X
     */
    public function testFutureListenerTimeout()
    {
        $loop = Factory::create();
        $stream = new ThroughStream();

        $loop->addPeriodicTimer(0.1, function(TimerInterface $timer) use ($stream) {
            $stream->on('data', function() {});
        });

        try {
            await(wait_for_stream_listeners($stream, $loop, 10, 9), $loop);
            $this->fail('Timeout should reject');
        } catch (\Exception $exception) {
            // Great
        }
    }
}