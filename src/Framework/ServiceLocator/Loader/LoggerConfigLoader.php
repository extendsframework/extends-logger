<?php
declare(strict_types=1);

namespace ExtendsFramework\Logger\Framework\ServiceLocator\Loader;

use ExtendsFramework\Http\Middleware\Chain\MiddlewareChainInterface;
use ExtendsFramework\Logger\Decorator\Backtrace\BacktraceDecorator;
use ExtendsFramework\Logger\Filter\Priority\PriorityFilter;
use ExtendsFramework\Logger\Framework\Http\Middleware\Logger\LoggerMiddleware;
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
use ExtendsFramework\ServiceLocator\Config\Loader\LoaderInterface;
use ExtendsFramework\ServiceLocator\Resolver\Factory\FactoryResolver;
use ExtendsFramework\ServiceLocator\Resolver\StaticFactory\StaticFactoryResolver;
use ExtendsFramework\ServiceLocator\ServiceLocatorInterface;

class LoggerConfigLoader implements LoaderInterface
{
    /**
     * Service locator config for Logger component.
     *
     * @return array
     */
    public function load(): array
    {
        return [
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
                'writers' => [],
            ],
            MiddlewareChainInterface::class => [
                LoggerMiddleware::class => 175,
            ],
        ];
    }
}
