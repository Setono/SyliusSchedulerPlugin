<?php

declare(strict_types=1);

namespace Setono\SyliusSchedulerPlugin\Validator;

use Cron\CronExpression;
use Setono\SyliusSchedulerPlugin\Validator\Constraints\CronExpression as CronExpressionConstraint;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class CronExpressionValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint): void
    {
        if (null === $value) {
            return;
        }

        /** @var CronExpressionConstraint $constraint */
        Assert::isInstanceOf($constraint, CronExpressionConstraint::class);

        if (null === $value) {
            return;
        }

        if (!CronExpression::isValidExpression($value)) {
            $this->context
                ->buildViolation($constraint->message)
                ->addViolation()
            ;
        }
    }
}
