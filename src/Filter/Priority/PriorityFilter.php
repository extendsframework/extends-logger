<?php
declare(strict_types=1);

namespace ExtendsFramework\Logger\Filter\Priority;

use ExtendsFramework\Logger\Filter\FilterInterface;
use ExtendsFramework\Logger\LogInterface;
use ExtendsFramework\Logger\Priority\Critical\CriticalPriority;
use ExtendsFramework\Logger\Priority\PriorityInterface;
use ExtendsFramework\Validator\Constraint\Comparison\GreaterThanConstraint;
use ExtendsFramework\Validator\Constraint\ConstraintInterface;

class PriorityFilter implements FilterInterface
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
        $this->priority = $priority ?: new CriticalPriority();
        $this->constraint = $constraint ?: new GreaterThanConstraint();
    }

    /**
     * @inheritDoc
     */
    public function filter(LogInterface $log): bool
    {
        return $this->constraint->validate($log->getPriority()->getValue(), $this->priority->getValue()) === null;
    }

    /**
     * @inheritDoc
     */
    public static function create(array $config): FilterInterface
    {
        return new static($config['priority'] ?? null, $config['constraint'] ?? null);
    }
}
