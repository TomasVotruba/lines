<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Lines202606\Symfony\Component\Console\ArgumentResolver\ValueResolver;

use Lines202606\Psr\Clock\ClockInterface;
use Lines202606\Symfony\Component\Console\Attribute\MapDateTime;
use Lines202606\Symfony\Component\Console\Attribute\Reflection\ReflectionMember;
use Lines202606\Symfony\Component\Console\Input\InputInterface;
/**
 * Resolves a \DateTime* instance as a command input argument or option.
 *
 * @author Benjamin Eberlei <kontakt@beberlei.de>
 * @author Tim Goudriaan <tim@codedmonkey.com>
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
final class DateTimeValueResolver implements ValueResolverInterface
{
    /**
     * @readonly
     * @var \Psr\Clock\ClockInterface|null
     */
    private $clock;
    public function __construct(?ClockInterface $clock = null)
    {
        $this->clock = $clock;
    }
    public function resolve(string $argumentName, InputInterface $input, ReflectionMember $member) : iterable
    {
        $type = $member->getType();
        if (!$type instanceof \ReflectionNamedType || !\is_a($type->getName(), \DateTimeInterface::class, \true)) {
            return [];
        }
        $attribute = $member->getAttribute(MapDateTime::class);
        $inputName = (($nullsafeVariable1 = $attribute) ? $nullsafeVariable1->argument : null) ?? (($nullsafeVariable2 = $attribute) ? $nullsafeVariable2->option : null) ?? $member->getInputName();
        // Try to get value from argument or option
        $value = null;
        if ($input->hasArgument($inputName)) {
            $value = $input->getArgument($inputName);
        } elseif ($input->hasOption($inputName)) {
            $value = $input->getOption($inputName);
        }
        /** @var class-string<\DateTimeImmutable>|class-string<\DateTime> $class */
        $class = \DateTimeInterface::class === $type->getName() ? \DateTimeImmutable::class : $type->getName();
        if (!$value) {
            if ($member->isNullable()) {
                return [null];
            }
            if (!$this->clock) {
                return [new $class()];
            }
            $value = $this->clock->now();
        }
        if ($value instanceof \DateTimeInterface) {
            return [$value instanceof $class ? $value : $class::createFromInterface($value)];
        }
        $format = ($nullsafeVariable3 = $attribute) ? $nullsafeVariable3->format : null;
        if (null !== $format) {
            $date = $class::createFromFormat($format, $value, ($nullsafeVariable4 = $this->clock) ? $nullsafeVariable4->now()->getTimeZone() : null);
            if (($class::getLastErrors() ?: ['warning_count' => 0])['warning_count']) {
                $date = \false;
            }
        } else {
            if (\false !== \filter_var($value, \FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]])) {
                $value = '@' . $value;
            }
            try {
                $date = new $class($value, ($nullsafeVariable5 = $this->clock) ? $nullsafeVariable5->now()->getTimeZone() : null);
            } catch (\Exception $exception) {
                $date = \false;
            }
        }
        if (!$date) {
            $message = \sprintf('Invalid date given for parameter "$%s".', $argumentName);
            if ($format) {
                $message .= \sprintf(' Expected format: "%s".', $format);
            }
            $message .= ' Use #[MapDateTime(format: \'your-format\')] to specify a custom format.';
            throw new \RuntimeException($message);
        }
        return [$date];
    }
}
