<?php

namespace Genkgo\Mail\Unit\Queue;

use Genkgo\Mail\AbstractTestCase;
use Genkgo\Mail\Exception\EmptyQueueException;
use Genkgo\Mail\GenericMessage;
use Genkgo\Mail\Header\Date;
use Genkgo\Mail\Queue\RedisQueue;
use Predis\ClientInterface;

final class RedisQueueTest extends AbstractTestCase
{
    /**
     * @test
     */
    public function it_can_store_a_message_in_redis()
    {
        $message = (new GenericMessage())
            ->withHeader(new Date(new \DateTimeImmutable('2017-01-01 18:15:00')));

        $client = $this->createMock(ClientInterface::class);

        $client
            ->expects($this->once())
            ->method('__call')
            ->with('rpush', ['queue', (string)$message]);

        $queue = new RedisQueue($client, 'queue');
        $queue->store($message);
    }

    /**
     * @test
     */
    public function it_can_fetch_a_message_from_redis()
    {
        $message = (new GenericMessage())
            ->withHeader(new Date(new \DateTimeImmutable('2017-01-01 18:15:00')));

        $client = $this->createMock(ClientInterface::class);

        $client
            ->expects($this->at(0))
            ->method('__call')
            ->with('rpush', ['queue', (string)$message]);

        $client
            ->expects($this->at(1))
            ->method('__call')
            ->with('lpop', ['queue'])
            ->willReturn((string)$message);

        $queue = new RedisQueue($client, 'queue');
        $queue->store($message);

        $this->assertEquals(
            (string) $message,
            (string) $queue->fetch()
        );
    }

    /**
     * @test
     */
    public function it_will_throw_when_no_message_left()
    {
        $this->expectException(EmptyQueueException::class);

        $message = (new GenericMessage())
            ->withHeader(new Date(new \DateTimeImmutable('2017-01-01 18:15:00')));

        $client = $this->createMock(ClientInterface::class);

        $client
            ->expects($this->at(0))
            ->method('__call')
            ->with('rpush', ['queue', (string)$message]);

        $client
            ->expects($this->at(1))
            ->method('__call')
            ->with('lpop', ['queue'])
            ->willReturn((string)$message);

        $client
            ->expects($this->at(2))
            ->method('__call')
            ->with('lpop', ['queue'])
            ->willThrowException(new EmptyQueueException());

        $queue = new RedisQueue($client, 'queue');
        $queue->store($message);

        $queue->fetch();
        $queue->fetch();
    }

}