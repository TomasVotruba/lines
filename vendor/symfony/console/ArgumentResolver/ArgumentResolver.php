<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Lines202606\Symfony\Component\Console\ArgumentResolver;

use Lines202606\Psr\Container\ContainerInterface;
use Lines202606\Symfony\Component\Console\ArgumentResolver\Exception\NearMissValueResolverException;
use Lines202606\Symfony\Component\Console\ArgumentResolver\Exception\ResolverNotFoundException;
use Lines202606\Symfony\Component\Console\ArgumentResolver\ValueResolver as Resolver;
use Lines202606\Symfony\Component\Console\ArgumentResolver\ValueResolver\ValueResolverInterface;
use Lines202606\Symfony\Component\Console\Attribute\Argument;
use Lines202606\Symfony\Component\Console\Attribute\Option;
use Lines202606\Symfony\Component\Console\Attribute\Reflection\ReflectionMember;
use Lines202606\Symfony\Component\Console\Attribute\ValueResolver;
use Lines202606\Symfony\Component\Console\Command\Command;
use Lines202606\Symfony\Component\Console\Cursor;
use Lines202606\Symfony\Component\Console\Input\InputInterface;
use Lines202606\Symfony\Component\Console\Input\RawInputInterface;
use Lines202606\Symfony\Component\Console\Output\OutputInterface;
use Lines202606\Symfony\Component\Console\Style\SymfonyStyle;
use Lines202606\Symfony\Contracts\Service\ServiceProviderInterface;
/**
 * Resolves the arguments passed to a console command.
 *
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
final class ArgumentResolver implements ArgumentResolverInterface
{
    /**
     * @var iterable<mixed, ValueResolverInterface>
     */
    private $argumentValueResolvers = [];
    /**
     * @var \Psr\Container\ContainerInterface|null
     */
    private $namedResolvers;
    /**
     * @param iterable<mixed, ValueResolverInterface> $argumentValueResolvers
     */
    public function __construct(iterable $argumentValueResolvers = [], ?ContainerInterface $namedResolvers = null)
    {
        $this->argumentValueResolvers = $argumentValueResolvers;
        $this->namedResolvers = $namedResolvers;
    }
    public function getArguments(InputInterface $input, callable $command, ?\ReflectionFunctionAbstract $reflector = null) : array
    {
        $reflector = $reflector ?? new \ReflectionFunction(\Closure::fromCallable($command));
        $argumentReflectors = [];
        foreach ($reflector->getParameters() as $param) {
            $argumentReflectors[$param->getName()] = new ReflectionMember($param);
        }
        $arguments = [];
        foreach ($argumentReflectors as $argumentName => $member) {
            $argumentValueResolvers = $this->argumentValueResolvers;
            $disabledResolvers = [];
            if ($this->namedResolvers && ($attributes = $member->getAttributes(ValueResolver::class))) {
                $resolverName = null;
                foreach ($attributes as $attribute) {
                    if ($attribute->disabled) {
                        $disabledResolvers[$attribute->resolver] = \true;
                    } elseif ($resolverName) {
                        throw new \LogicException(\sprintf('You can only pin one resolver per argument, but argument "$%s" of "%s()" has more.', $member->getName(), $member->getSourceName()));
                    } else {
                        $resolverName = $attribute->resolver;
                    }
                }
                if ($resolverName) {
                    if (!$this->namedResolvers->has($resolverName)) {
                        throw new ResolverNotFoundException($resolverName, $this->namedResolvers instanceof ServiceProviderInterface ? \array_keys($this->namedResolvers->getProvidedServices()) : []);
                    }
                    $argumentValueResolvers = [$this->namedResolvers->get($resolverName)];
                }
            }
            $valueResolverExceptions = [];
            foreach ($argumentValueResolvers as $name => $resolver) {
                if (isset($disabledResolvers[\is_int($name) ? \get_class($resolver) : $name])) {
                    continue;
                }
                try {
                    $count = 0;
                    foreach ($resolver->resolve($argumentName, $input, $member) as $argument) {
                        ++$count;
                        $arguments[] = $argument;
                    }
                } catch (NearMissValueResolverException $e) {
                    $valueResolverExceptions[] = $e;
                }
                if (1 < $count && !$member->isVariadic()) {
                    throw new \InvalidArgumentException(\sprintf('"%s::resolve()" must yield at most one value for non-variadic arguments.', \get_debug_type($resolver)));
                }
                if ($count) {
                    continue 2;
                }
            }
            // For variadic parameters with explicit input mapping, 0 values is valid
            if ($member->isVariadic() && (Argument::tryFrom($member->getMember()) || Option::tryFrom($member->getMember()))) {
                continue;
            }
            $type = $member->getType();
            $typeName = $type instanceof \ReflectionNamedType ? $type->getName() : null;
            if ($typeName && \in_array($typeName, [InputInterface::class, RawInputInterface::class, OutputInterface::class, SymfonyStyle::class, Cursor::class, \Lines202606\Symfony\Component\Console\Application::class, Command::class], \true)) {
                continue;
            }
            $reasons = \array_map(static function (NearMissValueResolverException $e) {
                return $e->getMessage();
            }, $valueResolverExceptions);
            if (!$reasons) {
                $reasons[] = \sprintf('The parameter has no #[Argument], #[Option], or #[MapInput] attribute, and its type "%s" cannot be auto-resolved.', $typeName ?? 'unknown');
                $reasons[] = 'Add an attribute to map this parameter to command input.';
            }
            throw new \RuntimeException(\sprintf('Could not resolve parameter "$%s" of command "%s".' . "\n\n" . 'Possible reasons:' . "\n" . '  • ' . \implode("\n  • ", $reasons), $member->getName(), $member->getSourceName()));
        }
        return $arguments;
    }
    /**
     * @return iterable<int, ValueResolverInterface>
     */
    public static function getDefaultArgumentValueResolvers() : iterable
    {
        $builtinTypeResolver = new Resolver\BuiltinTypeValueResolver();
        $backedEnumResolver = new Resolver\BackedEnumValueResolver();
        $dateTimeResolver = new Resolver\DateTimeValueResolver();
        $inputFileResolver = new Resolver\InputFileValueResolver();
        return [$backedEnumResolver, new Resolver\UidValueResolver(), $inputFileResolver, $builtinTypeResolver, new Resolver\MapInputValueResolver($builtinTypeResolver, $backedEnumResolver, $dateTimeResolver), $dateTimeResolver, new Resolver\DefaultValueResolver(), new Resolver\VariadicValueResolver()];
    }
}
