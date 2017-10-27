<?php
declare(strict_types=1);

namespace ExtendsFramework\Logger\Framework\ServiceLocator\Loader;

use ExtendsFramework\Logger\Decorator\Backtrace\BacktraceDecorator;
use ExtendsFramework\Logger\Filter\Priority\PriorityFilter;
use ExtendsFramework\Logger\Framework\ServiceLocator\Factory\Logger\LoggerFactory;
use ExtendsFramework\Logger\LoggerInterface;
use ExtendsFramework\Logger\Priority\Alert\AlertPriority;
use ExtendsFramework\Logger\Priority\Critical\CriticalPriority;
use ExtendsFramework\Logger\Priority\Debug\DebugPriority;
use ExtendsFramework\Logger\Priority\Emergency\EmergencyPriority;
use ExtendsFramework\Logger\Priority\Error\ErrorPriority;
use ExtendsFramework\Logger\Priority\Informational\InformationalPriority;
use ExtendsFramework\Logger\Priority\Notice\NoticePriority;
use ExtendsFramework\Logger\Priority\Warning\WarningPriority;
use ExtendsFramework\Logger\Writer\File\FileWriter;
use ExtendsFramework\Logger\Writer\Pdo\PdoWriter;
use ExtendsFramework\ServiceLocator\Resolver\Factory\FactoryResolver;
use ExtendsFramework\ServiceLocator\Resolver\StaticFactory\StaticFactoryResolver;
use ExtendsFramework\ServiceLocator\ServiceLocatorInterface;
use PHPUnit\Framework\TestCase;

class LoggerConfigLoaderTest extends TestCase
{
    /**
     * Load.
     *
     * Test that correct config will be returned.
     *
     * @covers \ExtendsFramework\Logger\Framework\ServiceLocator\Loader\LoggerConfigLoader::load()
     */
    public function testLoad(): void
    {
        $loader = new LoggerConfigLoader();

        $this->assertSame([
            ServiceLocatorInterface::class => [
                FactoryResolver::class => [
                    LoggerInterface::class => LoggerFactory::class,
                ],
                StaticFactoryResolver::class => [
                    BacktraceDecorator::class => BacktraceDecorator::class,
                    PriorityFilter::class => PriorityFilter::class,
                    AlertPriority::class => AlertPriority::class,
                    CriticalPriority::class => CriticalPriority::class,
                    DebugPriority::class => DebugPriority::class,
                    EmergencyPriority::class => EmergencyPriority::class,
                    ErrorPriority::class => ErrorPriority::class,
                    InformationalPriority::class => InformationalPriority::class,
                    NoticePriority::class => NoticePriority::class,
                    WarningPriority::class => WarningPriority::class,
                    FileWriter::class => FileWriter::class,
                    PdoWriter::class => PdoWriter::class,
                ],
            ],
            LoggerInterface::class => [
                'writers' => [
                    [
                        'name' => FileWriter::class,
                        'options' => [
                            'location' => './../data/log/',
                            'file_format' => 'Y-m-d',
                        ],
                        'filters' => [
                            [
                                'name' => PriorityFilter::class,
                            ],
                        ],
                        'decorators' => [
                            [
                                'name' => BacktraceDecorator::class,
                            ],
                        ],
                    ],
                ],
            ],
        ], $loader->load());
    }
}
