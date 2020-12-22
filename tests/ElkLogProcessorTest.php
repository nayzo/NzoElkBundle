<?php

namespace Nzo\ElkBundle\Tests;

use Nzo\ElkBundle\Encryptor\ElkEncryptor;
use Nzo\ElkBundle\Logger\ElkLogProcessor;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\RequestStack;

class ElkLogProcessorTest extends TestCase
{
    private $elkEncryptor;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->elkEncryptor = $this->getMockBuilder(ElkEncryptor::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @test
     * @dataProvider recordProvider
     */
    public function processRecord($logs)
    {
        $processor = new ElkLogProcessor(new RequestStack(), $this->elkEncryptor, 'app', 'local');
        $record = $processor->processRecord($logs);

        $this->assertCount(2, $record['extra']);
        $this->assertEquals('app', $record['extra']['app_name']);
        $this->assertEquals('local', $record['extra']['app_environment']);
        $this->assertContains($record['message'], ['info', 'warning', 'error']);
        $this->assertContains($record['channel'], ['ch_info', 'ch_warning', 'ch_error']);
    }

    /**
     * @return array
     */
    public function recordProvider()
    {
        return array(
            array(
                [
                    'message' => 'info',
                    'level' => Logger::INFO,
                    'level_name' => Logger::getLevelName(Logger::INFO),
                    'context' => [],
                    'channel' => 'ch_info',
                    'datetime' => new \DateTime(),
                    'extra' => [],
                ],
            ),
            array(
                [
                    'message' => 'warning',
                    'level' => Logger::WARNING,
                    'level_name' => Logger::getLevelName(Logger::WARNING),
                    'context' => [],
                    'channel' => 'ch_warning',
                    'datetime' => new \DateTime(),
                    'extra' => [],
                ],
            ),
            array(
                [
                    'message' => 'error',
                    'level' => Logger::ERROR,
                    'level_name' => Logger::getLevelName(Logger::ERROR),
                    'context' => [],
                    'channel' => 'ch_error',
                    'datetime' => new \DateTime(),
                    'extra' => [],
                ],
            ),
        );
    }
}
