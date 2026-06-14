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

use Lines202606\Symfony\Component\Console\Attribute\Argument;
use Lines202606\Symfony\Component\Console\Attribute\MapInput;
use Lines202606\Symfony\Component\Console\Attribute\Option;
use Lines202606\Symfony\Component\Console\Attribute\Reflection\ReflectionMember;
use Lines202606\Symfony\Component\Console\Exception\InputValidationFailedException;
use Lines202606\Symfony\Component\Console\Input\InputInterface;
use Lines202606\Symfony\Component\Validator\Validator\ValidatorInterface;
/**
 * Resolves the value of a input argument/option to an object holding the #[MapInput] attribute.
 *
 * @author Yonel Ceruto <open@yceruto.dev>
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
final class MapInputValueResolver implements ValueResolverInterface
{
    /**
     * @readonly
     * @var \Symfony\Component\Console\ArgumentResolver\ValueResolver\ValueResolverInterface
     */
    private $builtinTypeResolver;
    /**
     * @readonly
     * @var \Symfony\Component\Console\ArgumentResolver\ValueResolver\ValueResolverInterface
     */
    private $backedEnumResolver;
    /**
     * @readonly
     * @var \Symfony\Component\Console\ArgumentResolver\ValueResolver\ValueResolverInterface
     */
    private $dateTimeResolver;
    /**
     * @readonly
     * @var \Symfony\Component\Validator\Validator\ValidatorInterface|null
     */
    private $validator;
    public function __construct(ValueResolverInterface $builtinTypeResolver, ValueResolverInterface $backedEnumResolver, ValueResolverInterface $dateTimeResolver, ?ValidatorInterface $validator = null)
    {
        $this->builtinTypeResolver = $builtinTypeResolver;
        $this->backedEnumResolver = $backedEnumResolver;
        $this->dateTimeResolver = $dateTimeResolver;
        $this->validator = $validator;
    }
    public function resolve(string $argumentName, InputInterface $input, ReflectionMember $member) : iterable
    {
        if (!($attribute = MapInput::tryFrom($member->getMember()))) {
            return [];
        }
        $instance = $this->resolveMapInput($attribute, $input);
        $violations = (($nullsafeVariable1 = $this->validator) ? $nullsafeVariable1->validate($instance, null, $attribute->validationGroups) : null) ?? [];
        if (!\count($violations)) {
            return [$instance];
        }
        $map = $this->buildPropertyToInputMap($attribute);
        $messages = [];
        foreach ($violations as $violation) {
            $path = $violation->getPropertyPath();
            $label = $map[$path] ?? $path;
            $messages[] = $label . ': ' . $violation->getMessage();
        }
        throw new InputValidationFailedException(\implode("\n", $messages), $violations);
    }
    private function resolveMapInput(MapInput $mapInput, InputInterface $input) : object
    {
        $instance = $mapInput->getClass()->newInstanceWithoutConstructor();
        foreach ($mapInput->getDefinition() as $name => $spec) {
            // ignore required arguments that are not set yet (may happen in interactive mode)
            if ($spec instanceof Argument && $spec->isRequired() && \in_array($input->getArgument($spec->name), [null, []], \true)) {
                continue;
            }
            switch (\true) {
                case $spec instanceof Argument:
                    $instance->{$name} = $this->resolveArgumentSpec($spec, $mapInput->getClass()->getProperty($name), $input);
                    break;
                case $spec instanceof Option:
                    $instance->{$name} = $this->resolveOptionSpec($spec, $mapInput->getClass()->getProperty($name), $input);
                    break;
                case $spec instanceof MapInput:
                    $instance->{$name} = $this->resolveMapInput($spec, $input);
                    break;
            }
        }
        return $instance;
    }
    /**
     * @return array<string, string>
     */
    private function buildPropertyToInputMap(MapInput $mapInput, string $prefix = '') : array
    {
        $map = [];
        foreach ($mapInput->getDefinition() as $propertyName => $spec) {
            $path = $prefix . $propertyName;
            switch (\true) {
                case $spec instanceof Argument:
                    $map[$path] = $spec->name;
                    break;
                case $spec instanceof Option:
                    $map[$path] = '--' . $spec->name;
                    break;
                default:
                    $map[$path] = $path;
                    break;
            }
            if ($spec instanceof MapInput) {
                $map += $this->buildPropertyToInputMap($spec, $path . '.');
            }
        }
        return $map;
    }
    /**
     * @return mixed
     */
    private function resolveArgumentSpec(Argument $argument, \ReflectionProperty $property, InputInterface $input)
    {
        if (\is_subclass_of($argument->typeName, \BackedEnum::class)) {
            return \iterator_to_array(\is_array($this->backedEnumResolver->resolve($property->name, $input, new ReflectionMember($property))) ? new \ArrayIterator($this->backedEnumResolver->resolve($property->name, $input, new ReflectionMember($property))) : $this->backedEnumResolver->resolve($property->name, $input, new ReflectionMember($property)))[0] ?? null;
        }
        if (\is_a($argument->typeName, \DateTimeInterface::class, \true)) {
            return \iterator_to_array(\is_array($this->dateTimeResolver->resolve($property->name, $input, new ReflectionMember($property))) ? new \ArrayIterator($this->dateTimeResolver->resolve($property->name, $input, new ReflectionMember($property))) : $this->dateTimeResolver->resolve($property->name, $input, new ReflectionMember($property)))[0] ?? null;
        }
        return \iterator_to_array(\is_array($this->builtinTypeResolver->resolve($property->name, $input, new ReflectionMember($property))) ? new \ArrayIterator($this->builtinTypeResolver->resolve($property->name, $input, new ReflectionMember($property))) : $this->builtinTypeResolver->resolve($property->name, $input, new ReflectionMember($property)))[0] ?? null;
    }
    /**
     * @return mixed
     */
    private function resolveOptionSpec(Option $option, \ReflectionProperty $property, InputInterface $input)
    {
        if (\is_subclass_of($option->typeName, \BackedEnum::class)) {
            return \iterator_to_array(\is_array($this->backedEnumResolver->resolve($property->name, $input, new ReflectionMember($property))) ? new \ArrayIterator($this->backedEnumResolver->resolve($property->name, $input, new ReflectionMember($property))) : $this->backedEnumResolver->resolve($property->name, $input, new ReflectionMember($property)))[0] ?? null;
        }
        if (\is_a($option->typeName, \DateTimeInterface::class, \true)) {
            return \iterator_to_array(\is_array($this->dateTimeResolver->resolve($property->name, $input, new ReflectionMember($property))) ? new \ArrayIterator($this->dateTimeResolver->resolve($property->name, $input, new ReflectionMember($property))) : $this->dateTimeResolver->resolve($property->name, $input, new ReflectionMember($property)))[0] ?? null;
        }
        return \iterator_to_array(\is_array($this->builtinTypeResolver->resolve($property->name, $input, new ReflectionMember($property))) ? new \ArrayIterator($this->builtinTypeResolver->resolve($property->name, $input, new ReflectionMember($property))) : $this->builtinTypeResolver->resolve($property->name, $input, new ReflectionMember($property)))[0] ?? null;
    }
}
