<?php
declare(strict_types=1);

namespace ExtendsFramework\Logger\Framework\ServiceLocator\Factory\Logger;

use ExtendsFramework\Logger\Logger;
use ExtendsFramework\Logger\LoggerInterface;
use ExtendsFramework\Logger\Writer\WriterInterface;
use ExtendsFramework\ServiceLocator\Resolver\Factory\ServiceFactoryInterface;
use ExtendsFramework\ServiceLocator\ServiceLocatorException;
use ExtendsFramework\ServiceLocator\ServiceLocatorInterface;

class LoggerFactory implements ServiceFactoryInterface
{
    /**
     * @inheritDoc
     */
    public function createService(string $key, ServiceLocatorInterface $serviceLocator, array $extra = null): object
    {
        $config = $serviceLocator->getConfig();
        $config = $config[LoggerInterface::class] ?? [];

        $logger = new Logger();
        foreach ($config['writers'] ?? [] as $writer) {
            $logger->addWriter(
                $this->getWriter($serviceLocator, $writer['name'], $writer['options'] ?? [])
            );
        }

        return $logger;
    }

    /**
     * Get writer for name with options.
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param string                  $name
     * @param array                   $options
     * @return WriterInterface
     * @throws ServiceLocatorException
     */
    protected function getWriter(ServiceLocatorInterface $serviceLocator, string $name, array $options): object
    {
        return $serviceLocator->getService($name, $options);
    }
}
