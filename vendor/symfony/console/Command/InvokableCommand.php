<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Lines202606\Symfony\Component\Console\Command;

use Lines202606\Symfony\Component\Console\Application;
use Lines202606\Symfony\Component\Console\ArgumentResolver\ArgumentResolver;
use Lines202606\Symfony\Component\Console\ArgumentResolver\ArgumentResolverInterface;
use Lines202606\Symfony\Component\Console\Attribute\Argument;
use Lines202606\Symfony\Component\Console\Attribute\Interact;
use Lines202606\Symfony\Component\Console\Attribute\MapInput;
use Lines202606\Symfony\Component\Console\Attribute\Option;
use Lines202606\Symfony\Component\Console\Cursor;
use Lines202606\Symfony\Component\Console\Input\InputArgument;
use Lines202606\Symfony\Component\Console\Input\InputDefinition;
use Lines202606\Symfony\Component\Console\Input\InputInterface;
use Lines202606\Symfony\Component\Console\Input\RawInputInterface;
use Lines202606\Symfony\Component\Console\Interaction\Interaction;
use Lines202606\Symfony\Component\Console\Output\OutputInterface;
use Lines202606\Symfony\Component\Console\Style\SymfonyStyle;
/**
 * Represents an invokable command.
 *
 * @author Yonel Ceruto <open@yceruto.dev>
 *
 * @internal
 */
class InvokableCommand implements SignalableCommandInterface
{
    /**
     * @readonly
     * @var \Symfony\Component\Console\Command\Command
     */
    private $command;
    /**
     * @var \Symfony\Component\Console\ArgumentResolver\ArgumentResolverInterface|null
     */
    private $argumentResolver;
    /**
     * @readonly
     * @var \Symfony\Component\Console\Command\SignalableCommandInterface|null
     */
    private $signalableCommand;
    /**
     * @readonly
     * @var \ReflectionFunction
     */
    private $invokable;
    /**
     * @var list<Interaction>|null
     */
    private $interactions;
    private $code;
    public function __construct(Command $command, callable $code, ?ArgumentResolverInterface $argumentResolver = null)
    {
        $this->command = $command;
        $this->argumentResolver = $argumentResolver;
        $this->code = $code;
        $this->signalableCommand = $code instanceof SignalableCommandInterface ? $code : null;
        $this->invokable = new \ReflectionFunction($this->getClosure($code));
    }
    /**
     * Invokes a callable with parameters generated from the input interface.
     */
    public function __invoke(InputInterface $input, OutputInterface $output) : int
    {
        $statusCode = $this->invokable->invoke(...$this->getParameters($this->invokable, $input, $output));
        if (!\is_int($statusCode)) {
            throw new \TypeError(\sprintf('The command "%s" must return an integer value in the "%s" method, but "%s" was returned.', $this->command->getName(), $this->invokable->getName(), \get_debug_type($statusCode)));
        }
        return $statusCode;
    }
    /**
     * Configures the input definition from an invokable-defined function.
     *
     * Processes the parameters of the reflection function to extract and
     * add arguments or options to the provided input definition.
     */
    public function configure(InputDefinition $definition) : void
    {
        foreach ($this->invokable->getParameters() as $parameter) {
            if ($argument = Argument::tryFrom($parameter)) {
                $definition->addArgument($argument->toInputArgument());
                continue;
            }
            if ($option = Option::tryFrom($parameter)) {
                $definition->addOption($option->toInputOption());
                continue;
            }
            if ($input = MapInput::tryFrom($parameter)) {
                $inputArguments = \array_map(static function (Argument $a) {
                    return $a->toInputArgument();
                }, \iterator_to_array(\is_array($input->getArguments()) ? new \ArrayIterator($input->getArguments()) : $input->getArguments(), \false));
                // make sure optional arguments are defined after required ones
                \usort($inputArguments, static function (InputArgument $a, InputArgument $b) {
                    return (int) $b->isRequired() - (int) $a->isRequired();
                });
                foreach ($inputArguments as $inputArgument) {
                    $definition->addArgument($inputArgument);
                }
                foreach ($input->getOptions() as $option) {
                    $definition->addOption($option->toInputOption());
                }
            }
        }
    }
    public function getCode() : callable
    {
        return $this->code;
    }
    private function getClosure(callable $code) : \Closure
    {
        if (!$code instanceof \Closure) {
            return \Closure::fromCallable($code);
        }
        if (null !== (new \ReflectionFunction($code))->getClosureThis()) {
            return $code;
        }
        \set_error_handler(static function () {
        });
        try {
            if ($c = \Closure::bind($code, $this->command)) {
                $code = $c;
            }
        } finally {
            \restore_error_handler();
        }
        return $code;
    }
    private function getParameters(\ReflectionFunction $function, InputInterface $input, OutputInterface $output) : array
    {
        $coreUtilities = [];
        $needsArgumentResolver = \false;
        foreach ($function->getParameters() as $index => $param) {
            $type = $param->getType();
            if ($type instanceof \ReflectionNamedType) {
                switch ($type->getName()) {
                    case InputInterface::class:
                        $argument = $input;
                        break;
                    case RawInputInterface::class:
                        $argument = $input;
                        break;
                    case OutputInterface::class:
                        $argument = $output;
                        break;
                    case SymfonyStyle::class:
                        $argument = new SymfonyStyle($input, $output, ($nullsafeVariable4 = $this->command->getApplication()) ? $nullsafeVariable4->getDispatcher() : null);
                        break;
                    case Cursor::class:
                        $argument = new Cursor($output);
                        break;
                    case Application::class:
                        $argument = $this->command->getApplication();
                        break;
                    case Command::class:
                    case self::class:
                        $argument = $this->command;
                        break;
                    default:
                        $argument = null;
                        break;
                }
                if (null !== $argument) {
                    $coreUtilities[$index] = $argument;
                    continue;
                }
            }
            $needsArgumentResolver = \true;
        }
        if (!$needsArgumentResolver) {
            return $coreUtilities;
        }
        if (null === $this->argumentResolver) {
            $this->argumentResolver = (($nullsafeVariable1 = $this->command->getApplication()) ? $nullsafeVariable1->getArgumentResolver() : null) ?? new ArgumentResolver(ArgumentResolver::getDefaultArgumentValueResolvers());
        }
        $closure = $function->getClosure();
        $resolvedArgs = $this->argumentResolver->getArguments($input, $closure, $function);
        $parameters = [];
        $resolvedIndex = 0;
        foreach ($function->getParameters() as $index => $param) {
            if (isset($coreUtilities[$index])) {
                $parameters[] = $coreUtilities[$index];
            } elseif ($param->isVariadic()) {
                // Variadic parameters consume all remaining resolved arguments
                $parameters = \array_merge($parameters, \array_slice($resolvedArgs, $resolvedIndex));
                break;
            } else {
                $parameters[] = $resolvedArgs[$resolvedIndex++] ?? null;
            }
        }
        return $parameters;
    }
    public function getSubscribedSignals() : array
    {
        return (($nullsafeVariable2 = $this->signalableCommand) ? $nullsafeVariable2->getSubscribedSignals() : null) ?? [];
    }
    /**
     * @return int|false
     * @param int|false $previousExitCode
     */
    public function handleSignal(int $signal, $previousExitCode = 0)
    {
        return (($nullsafeVariable3 = $this->signalableCommand) ? $nullsafeVariable3->handleSignal($signal, $previousExitCode) : null) ?? \false;
    }
    public function isInteractive() : bool
    {
        if (null === $this->interactions) {
            $this->collectInteractions();
        }
        return [] !== $this->interactions;
    }
    public function interact(InputInterface $input, OutputInterface $output) : void
    {
        if (null === $this->interactions) {
            $this->collectInteractions();
        }
        foreach ($this->interactions as $interaction) {
            $interaction->interact($input, $output, \Closure::fromCallable([$this, 'getParameters']));
        }
    }
    private function collectInteractions() : void
    {
        $invokableThis = $this->invokable->getClosureThis();
        $this->interactions = [];
        foreach ($this->invokable->getParameters() as $parameter) {
            if ($spec = Argument::tryFrom($parameter)) {
                if ($attribute = $spec->getInteractiveAttribute()) {
                    $this->interactions[] = new Interaction($invokableThis, $attribute);
                }
                continue;
            }
            if ($spec = MapInput::tryFrom($parameter)) {
                $this->interactions = \array_merge($this->interactions, \is_array($spec->getPropertyInteractions()) ? $spec->getPropertyInteractions() : \iterator_to_array($spec->getPropertyInteractions()), \is_array($spec->getMethodInteractions()) ? $spec->getMethodInteractions() : \iterator_to_array($spec->getMethodInteractions()));
            }
        }
        if (!($class = $this->invokable->getClosureCalledClass())) {
            return;
        }
        foreach ($class->getMethods() as $method) {
            if ($attribute = Interact::tryFrom($method)) {
                $this->interactions[] = new Interaction($invokableThis, $attribute);
            }
        }
    }
}
