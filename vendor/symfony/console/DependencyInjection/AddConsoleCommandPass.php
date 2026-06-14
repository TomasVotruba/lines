<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Lines202606\Symfony\Component\Console\DependencyInjection;

use Lines202606\Symfony\Component\Console\Attribute\AsCommand;
use Lines202606\Symfony\Component\Console\Command\Command;
use Lines202606\Symfony\Component\Console\Command\LazyCommand;
use Lines202606\Symfony\Component\Console\CommandLoader\ContainerCommandLoader;
use Lines202606\Symfony\Component\DependencyInjection\Argument\ServiceClosureArgument;
use Lines202606\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Lines202606\Symfony\Component\DependencyInjection\Compiler\ServiceLocatorTagPass;
use Lines202606\Symfony\Component\DependencyInjection\ContainerBuilder;
use Lines202606\Symfony\Component\DependencyInjection\Definition;
use Lines202606\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Lines202606\Symfony\Component\DependencyInjection\Reference;
use Lines202606\Symfony\Component\DependencyInjection\TypedReference;
/**
 * Registers console commands.
 *
 * @author Grégoire Pineau <lyrixx@lyrixx.info>
 */
class AddConsoleCommandPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container) : void
    {
        $commandServices = [];
        $lazyCommandMap = [];
        $lazyCommandRefs = [];
        $serviceIds = [];
        foreach ($container->findTaggedServiceIds('console.command', \true) as $id => $tags) {
            foreach ($tags as $tag) {
                $commandServices[$id][$tag['method'] ?? '__invoke'][] = $tag;
            }
        }
        foreach ($commandServices as $id => $commands) {
            $definition = $container->getDefinition($id);
            $class = $container->getParameterBag()->resolveValue($definition->getClass());
            if (!($r = $container->getReflectionClass($class))) {
                throw new InvalidArgumentException(\sprintf('Class "%s" used for service "%s" cannot be found.', $class, $id));
            }
            foreach ($commands as $tags) {
                $this->registerCommand($container, $r, $id, $class, $tags, $definition, $serviceIds, $lazyCommandMap, $lazyCommandRefs);
            }
        }
        $container->register('console.command_loader', ContainerCommandLoader::class)->setPublic(\true)->addTag('container.no_preload')->setArguments([ServiceLocatorTagPass::register($container, $lazyCommandRefs), $lazyCommandMap]);
        $container->setParameter('console.command.ids', $serviceIds);
    }
    private function registerCommand(ContainerBuilder $container, \ReflectionClass $reflection, string $id, string $class, array $tags, Definition $definition, array &$serviceIds, array &$lazyCommandMap, array &$lazyCommandRefs) : void
    {
        if (!$reflection->isSubclassOf(Command::class)) {
            $method = $tags[0]['method'] ?? '__invoke';
            if (!$reflection->hasMethod($method)) {
                throw new InvalidArgumentException(\sprintf('The service "%s" tagged "%s" must either be a subclass of "%s" or have an "%s()" method.', $id, 'console.command', Command::class, $method));
            }
            $reflection = $reflection->getMethod($method);
            if (!$reflection->isPublic() || $reflection->isStatic()) {
                throw new InvalidArgumentException(\sprintf('The method "%s::%s()" must be public and non-static to be used as a console command.', $class, $method));
            }
            if ('__invoke' === $method) {
                $callableRef = new Reference($id);
                $id .= '.command';
            } else {
                $callableRef = [new Reference($id), $method];
                $id .= '.' . $method . '.command';
            }
            $class = Command::class;
            $closureDefinition = (new Definition(\Closure::class))->setFactory([\Closure::class, 'fromCallable'])->setArguments([$callableRef]);
            $definition = $container->register($id, $class)->addMethodCall('setCode', [$closureDefinition]);
        } elseif (isset($tags[0]['method'])) {
            throw new InvalidArgumentException(\sprintf('The service "%s" tagged "console.command" cannot define a method command when it is a subclass of "%s".', $id, Command::class));
        }
        $definition->addTag('container.no_preload');
        $attribute = $this->getCommandAttribute($reflection);
        $defaultName = ($nullsafeVariable1 = $attribute) ? $nullsafeVariable1->name : null;
        $aliases = \str_replace('%', '%%', $tags[0]['command'] ?? $defaultName ?? '');
        $aliases = \explode('|', $aliases);
        $commandName = \array_shift($aliases);
        if ($isHidden = '' === $commandName) {
            $commandName = \array_shift($aliases);
        }
        if (null === $commandName) {
            if ($definition->isPrivate() || $definition->hasTag('container.private')) {
                $commandId = 'console.command.public_alias.' . $id;
                $container->setAlias($commandId, $id)->setPublic(\true);
                $id = $commandId;
            }
            $serviceIds[] = $id;
            return;
        }
        $description = $tags[0]['description'] ?? null;
        $help = $tags[0]['help'] ?? null;
        $usages = $tags[0]['usages'] ?? null;
        unset($tags[0]);
        $lazyCommandMap[$commandName] = $id;
        $lazyCommandRefs[$id] = new TypedReference($id, $class);
        foreach ($aliases as $alias) {
            $lazyCommandMap[$alias] = $id;
        }
        foreach ($tags as $tag) {
            if (isset($tag['command'])) {
                $aliases[] = $tag['command'];
                $lazyCommandMap[$tag['command']] = $id;
            }
            $description = $description ?? $tag['description'] ?? null;
            $help = $help ?? $tag['help'] ?? null;
            $usages = $usages ?? $tag['usages'] ?? null;
        }
        $definition->addMethodCall('setName', [$commandName]);
        if ($aliases) {
            $definition->addMethodCall('setAliases', [$aliases]);
        }
        if ($isHidden) {
            $definition->addMethodCall('setHidden', [\true]);
        }
        if ($help = $help ?? (($nullsafeVariable4 = $attribute) ? $nullsafeVariable4->help : null)) {
            $definition->addMethodCall('setHelp', [\str_replace('%', '%%', $help)]);
        }
        if ($usages = $usages ?? (($nullsafeVariable5 = $attribute) ? $nullsafeVariable5->usages : null)) {
            foreach ($usages as $usage) {
                $definition->addMethodCall('addUsage', [$usage]);
            }
        }
        if ($description = $description ?? (($nullsafeVariable6 = $attribute) ? $nullsafeVariable6->description : null)) {
            $escapedDescription = \str_replace('%', '%%', $description);
            $definition->addMethodCall('setDescription', [$escapedDescription]);
            $container->register('.' . $id . '.lazy', LazyCommand::class)->setArguments([$commandName, $aliases, $escapedDescription, $isHidden, new ServiceClosureArgument($lazyCommandRefs[$id])]);
            $lazyCommandRefs[$id] = new Reference('.' . $id . '.lazy');
        }
    }
    /**
     * @param \ReflectionClass|\ReflectionMethod $reflection
     */
    private function getCommandAttribute($reflection) : ?AsCommand
    {
        /** @var AsCommand|null $attribute */
        if ($attribute = ($nullsafeVariable2 = (\method_exists($reflection, 'getAttributes') ? $reflection->getAttributes(AsCommand::class) : [])[0] ?? null) ? $nullsafeVariable2->newInstance() : null) {
            return $attribute;
        }
        if ($reflection instanceof \ReflectionMethod && '__invoke' === $reflection->getName()) {
            return ($nullsafeVariable3 = (\method_exists($reflection->getDeclaringClass(), 'getAttributes') ? $reflection->getDeclaringClass()->getAttributes(AsCommand::class) : [])[0] ?? null) ? $nullsafeVariable3->newInstance() : null;
        }
        return null;
    }
}
