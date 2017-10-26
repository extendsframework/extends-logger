<?php
declare(strict_types=1);

namespace ExtendsFramework\Logger\Framework\ServiceLocator\Factory;

use ExtendsFramework\Logger\Logger;
use ExtendsFramework\Logger\LoggerInterface;
use ExtendsFramework\ServiceLocator\Resolver\Factory\ServiceFactoryInterface;
use ExtendsFramework\ServiceLocator\ServiceLocatorInterface;

class LoggerFactory implements ServiceFactoryInterface
{
    /**
     * @inheritDoc
     */
    public function createService(string $key, ServiceLocatorInterface $serviceLocator, array $extra = null): LoggerInterface
    {
        $config = $serviceLocator->getConfig();
        $config = $config[LoggerInterface::class] ?? [];

        $logger = new Logger();
        foreach ($config['writers'] ?? [] as $writer) {
            $logger->addWriter(
                $serviceLocator->getService($writer['name'], $writer['options'] ?? [])
            );
        }

        return $logger;
    }
}
