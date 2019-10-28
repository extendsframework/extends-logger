<?php
declare(strict_types=1);

namespace ExtendsFramework\Logger\Framework\ServiceLocator\Factory\Logger;

use ExtendsFramework\Logger\LoggerInterface;
use ExtendsFramework\Logger\Writer\WriterInterface;
use ExtendsFramework\ServiceLocator\ServiceLocatorInterface;
use PHPUnit\Framework\TestCase;

class LoggerFactoryTest extends TestCase
{
    /**
     * Create service.
     *
     * Test that factory will return an instance of LoggerInterface.
     *
     * @covers \ExtendsFramework\Logger\Framework\ServiceLocator\Factory\Logger\LoggerFactory::createService()
     * @covers \ExtendsFramework\Logger\Framework\ServiceLocator\Factory\Logger\LoggerFactory::getWriter()
     */
    public function testCreateService(): void
    {
        $serviceLocator = $this->createMock(ServiceLocatorInterface::class);
        $serviceLocator
            ->expects($this->once())
            ->method('getConfig')
            ->willReturn([
                LoggerInterface::class => [
                    'writers' => [
                        [
                            'name' => WriterInterface::class,
                            'options' => [
                                'foo' => 'bar',
                            ],
                        ],
                    ],
                ],
            ]);

        $serviceLocator
            ->expects($this->once())
            ->method('getService')
            ->with(WriterInterface::class, ['foo' => 'bar'])
            ->willReturn($this->createMock(WriterInterface::class));

        /**
         * @var ServiceLocatorInterface $serviceLocator
         */
        $factory = new LoggerFactory();
        $logger = $factory->createService(LoggerInterface::class, $serviceLocator);

        $this->assertInstanceOf(LoggerInterface::class, $logger);
    }
}
