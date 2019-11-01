<?php
declare(strict_types=1);

namespace ExtendsFramework\Logger\Filter\Priority;

use ExtendsFramework\Logger\Filter\FilterInterface;
use ExtendsFramework\Logger\LogInterface;
use ExtendsFramework\Logger\Priority\Critical\CriticalPriority;
use ExtendsFramework\Logger\Priority\PriorityInterface;
use ExtendsFramework\ServiceLocator\Resolver\StaticFactory\StaticFactoryInterface;
use ExtendsFramework\ServiceLocator\ServiceLocatorException;
use ExtendsFramework\ServiceLocator\ServiceLocatorInterface;
use ExtendsFramework\Validator\Comparison\GreaterThanValidator;
use ExtendsFramework\Validator\ValidatorInterface;

class PriorityFilter implements FilterInterface, StaticFactoryInterface
{
    /**
     * Comparison operator.
     *
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * Create new priority filter.
     *
     * @param PriorityInterface|null  $priority
     * @param ValidatorInterface|null $validator
     */
    public function __construct(PriorityInterface $priority = null, ValidatorInterface $validator = null)
    {
        $this->validator = $validator ?: new GreaterThanValidator(($priority ?: new CriticalPriority())->getValue());
    }

    /**
     * @inheritDoc
     * @throws ServiceLocatorException
     */
    public static function factory(string $key, ServiceLocatorInterface $serviceLocator, array $extra = null): object
    {
        if (array_key_exists('priority', $extra)) {
            $priority = $serviceLocator->getService($extra['priority']['name'], $extra['priority']['options'] ?? []);
        }

        if (array_key_exists('validator', $extra)) {
            $validator = $serviceLocator->getService(
                $extra['validator']['name'],
                $extra['validator']['options'] ?? []
            );
        }

        /** @noinspection PhpParamsInspection */
        return new static(
            $priority ?? null,
            $validator ?? null
        );
    }

    /**
     * @inheritDoc
     */
    public function filter(LogInterface $log): bool
    {
        return $this->validator
            ->validate(
                $log
                    ->getPriority()
                    ->getValue()
            )
            ->isValid();
    }
}
