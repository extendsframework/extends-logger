<?php
declare(strict_types=1);

namespace ExtendsFramework\Logger\Filter\Priority;

use ExtendsFramework\Logger\Filter\FilterInterface;
use ExtendsFramework\Logger\LogInterface;
use ExtendsFramework\Logger\Priority\Critical\CriticalPriority;
use ExtendsFramework\Logger\Priority\PriorityInterface;
use ExtendsFramework\ServiceLocator\Resolver\StaticFactory\StaticFactoryInterface;
use ExtendsFramework\ServiceLocator\ServiceLocatorInterface;
use ExtendsFramework\Validator\Comparison\GreaterThanValidator;
use ExtendsFramework\Validator\ValidatorInterface;

class PriorityFilter implements FilterInterface, StaticFactoryInterface
{
    /**
     * Priority value to compare.
     *
     * @var PriorityInterface
     */
    protected $priority;

    /**
     * Comparison operator.
     *
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * Create new priority filter.
     *
     * @param PriorityInterface|null  $priority
     * @param ValidatorInterface|null $constraint
     */
    public function __construct(PriorityInterface $priority = null, ValidatorInterface $constraint = null)
    {
        $this->priority = $priority;
        $this->validator = $constraint;
    }

    /**
     * @inheritDoc
     */
    public function filter(LogInterface $log): bool
    {
        return $this
                ->getValidator()
                ->validate($log->getPriority()->getValue())
                ->isValid();
    }

    /**
     * @inheritDoc
     */
    public static function factory(string $key, ServiceLocatorInterface $serviceLocator, array $extra = null): object
    {
        if (array_key_exists('priority', $extra)) {
            $priority = $serviceLocator->getService($extra['priority']['name'], $extra['priority']['options'] ?? []);
        }

        if (array_key_exists('validator', $extra)) {
            $constraint = $serviceLocator->getService(
                $extra['validator']['name'],
                $extra['validator']['options'] ?? []
            );
        }

        return new static(
            $priority ?? null,
            $constraint ?? null
        );
    }

    /**
     * Get priority.
     *
     * @return PriorityInterface
     */
    protected function getPriority(): PriorityInterface
    {
        if ($this->priority === null) {
            $this->priority = new CriticalPriority();
        }

        return $this->priority;
    }

    /**
     * Get validator.
     *
     * @return ValidatorInterface
     */
    protected function getValidator(): ValidatorInterface
    {
        if ($this->validator === null) {
            $this->validator = new GreaterThanValidator(
                $this->getPriority()->getValue()
            );
        }

        return $this->validator;
    }
}
