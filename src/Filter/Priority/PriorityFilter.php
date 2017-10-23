<?php
declare(strict_types=1);

namespace ExtendsFramework\Logger\Filter\Priority;

use ExtendsFramework\Logger\Filter\FilterInterface;
use ExtendsFramework\Logger\LogInterface;
use ExtendsFramework\Logger\Priority\Critical\CriticalPriority;
use ExtendsFramework\Logger\Priority\PriorityInterface;
use ExtendsFramework\ServiceLocator\Resolver\StaticFactory\StaticFactoryInterface;
use ExtendsFramework\ServiceLocator\ServiceLocatorInterface;
use ExtendsFramework\Validator\Constraint\Comparison\GreaterThanConstraint;
use ExtendsFramework\Validator\Constraint\ConstraintInterface;

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
     * @var ConstraintInterface
     */
    protected $constraint;

    /**
     * Create new priority filter.
     *
     * @param PriorityInterface|null   $priority
     * @param ConstraintInterface|null $constraint
     */
    public function __construct(PriorityInterface $priority = null, ConstraintInterface $constraint = null)
    {
        $this->priority = $priority;
        $this->constraint = $constraint;
    }

    /**
     * @inheritDoc
     */
    public function filter(LogInterface $log): bool
    {
        return $this
                ->getConstraint()
                ->validate($log->getPriority()->getValue(), $this->getPriority()->getValue()) === null;
    }

    /**
     * @inheritDoc
     */
    public static function factory(string $key, ServiceLocatorInterface $serviceLocator, array $extra = null): FilterInterface
    {
        if (array_key_exists('priority', $extra) === true) {
            $priority = $serviceLocator->getService($extra['priority']['name'], $extra['priority']['options'] ?? []);
        }

        if (array_key_exists('constraint', $extra) === true) {
            $constraint = $serviceLocator->getService($extra['constraint']['name'], $extra['constraint']['options'] ?? []);
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
     * Get constraint.
     *
     * @return ConstraintInterface
     */
    protected function getConstraint(): ConstraintInterface
    {
        if ($this->constraint === null) {
            $this->constraint = new GreaterThanConstraint();
        }

        return $this->constraint;
    }
}
