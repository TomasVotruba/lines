<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Lines202606\Symfony\Component\Console\Attribute;

use Lines202606\Symfony\Component\Console\Attribute\Reflection\ReflectionMember;
use Lines202606\Symfony\Component\Console\Completion\CompletionInput;
use Lines202606\Symfony\Component\Console\Completion\Suggestion;
use Lines202606\Symfony\Component\Console\Exception\LogicException;
use Lines202606\Symfony\Component\Console\Input\InputOption;
use Lines202606\Symfony\Component\String\UnicodeString;
#[\Attribute(\Attribute::TARGET_PARAMETER | \Attribute::TARGET_PROPERTY)]
class Option
{
    /**
     * @var string
     */
    public $description = '';
    /**
     * @var string
     */
    public $name = '';
    /**
     * @var array|string|null
     */
    public $shortcut = null;
    public const ALLOWED_UNION_TYPES = ['bool|string', 'bool|int', 'bool|float'];
    /**
     * @var mixed
     */
    public $default = null;
    /**
     * @var mixed[]|\Closure
     */
    public $suggestedValues;
    /**
     * @internal
     *
     * @var string|class-string<\BackedEnum>
     */
    public $typeName = '';
    /** @internal
     * @var bool */
    public $allowNull = \false;
    /**
     * @var int|null
     */
    private $mode;
    /**
     * @var string
     */
    private $memberName = '';
    /**
     * @var string
     */
    private $sourceName = '';
    /**
     * Represents a console command --option definition.
     *
     * If unset, the `name` value will be inferred from the parameter definition.
     *
     * @param array|string|null                                                          $shortcut        The shortcuts, can be null, a string of shortcuts delimited by | or an array of shortcuts
     * @param array<string|Suggestion>|callable(CompletionInput):list<string|Suggestion> $suggestedValues The values used for input completion
     */
    public function __construct(string $description = '', string $name = '', $shortcut = null, $suggestedValues = [])
    {
        $this->description = $description;
        $this->name = $name;
        $this->shortcut = $shortcut;
        $this->suggestedValues = \is_callable($suggestedValues) ? \Closure::fromCallable($suggestedValues) : $suggestedValues;
    }
    /**
     * @internal
     * @param \ReflectionParameter|\ReflectionProperty $member
     */
    public static function tryFrom($member) : ?self
    {
        $reflection = new ReflectionMember($member);
        if (!($self = $reflection->getAttribute(self::class))) {
            return null;
        }
        $self->memberName = $reflection->getMemberName();
        $self->sourceName = $reflection->getSourceName();
        $name = $reflection->getName();
        $type = $reflection->getType();
        // Variadic parameters implicitly default to an empty array
        if (!$reflection->isVariadic() && !$reflection->hasDefaultValue()) {
            throw new LogicException(\sprintf('The option %s "$%s" of "%s" must declare a default value.', $self->memberName, $name, $self->sourceName));
        }
        if (!$self->name) {
            $self->name = (new UnicodeString($name))->kebab();
        }
        $self->default = $reflection->isVariadic() ? [] : $reflection->getDefaultValue();
        $self->allowNull = $reflection->isNullable();
        if ($type instanceof \ReflectionUnionType) {
            return $self->handleUnion($type);
        }
        if (!$type instanceof \ReflectionNamedType) {
            throw new LogicException(\sprintf('The %s "$%s" of "%s" must have a named type. Untyped or Intersection types are not supported for command options.', $self->memberName, $name, $self->sourceName));
        }
        $self->typeName = $type->getName();
        if ('bool' === $self->typeName && $self->allowNull && \in_array($self->default, [\true, \false], \true)) {
            throw new LogicException(\sprintf('The option %s "$%s" of "%s" must not be nullable when it has a default boolean value.', $self->memberName, $name, $self->sourceName));
        }
        if ($self->allowNull && null !== $self->default) {
            throw new LogicException(\sprintf('The option %s "$%s" of "%s" must either be not-nullable or have a default of null.', $self->memberName, $name, $self->sourceName));
        }
        if ('bool' === $self->typeName) {
            $self->mode = InputOption::VALUE_NONE;
            if (\false !== $self->default) {
                $self->mode |= InputOption::VALUE_NEGATABLE;
            }
        } elseif ('array' === $self->typeName || $reflection->isVariadic()) {
            $self->mode = InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY;
        } else {
            $self->mode = InputOption::VALUE_REQUIRED;
        }
        if (\is_array($self->suggestedValues) && !\is_callable($self->suggestedValues) && 2 === \count($self->suggestedValues) && ($instance = $reflection->getSourceThis()) && \get_class($instance) === $self->suggestedValues[0] && \is_callable([$instance, $self->suggestedValues[1]])) {
            $self->suggestedValues = [$instance, $self->suggestedValues[1]];
        }
        if (\is_subclass_of($self->typeName, \BackedEnum::class) && !$self->suggestedValues) {
            $self->suggestedValues = \array_column($self->typeName::cases(), 'value');
        }
        return $self;
    }
    /**
     * @internal
     */
    public function toInputOption() : InputOption
    {
        $default = InputOption::VALUE_NONE === (InputOption::VALUE_NONE & $this->mode) ? null : $this->default;
        $suggestedValues = \is_callable($this->suggestedValues) ? \Closure::fromCallable($this->suggestedValues) : $this->suggestedValues;
        return new InputOption($this->name, $this->shortcut, $this->mode, $this->description, $default, $suggestedValues);
    }
    private function handleUnion(\ReflectionUnionType $type) : self
    {
        $types = \array_map(static function (\ReflectionType $t) {
            return $t instanceof \ReflectionNamedType ? $t->getName() : null;
        }, $type->getTypes());
        \sort($types);
        $this->typeName = \implode('|', \array_filter($types));
        if (!\in_array($this->typeName, self::ALLOWED_UNION_TYPES, \true)) {
            throw new LogicException(\sprintf('The union type for %s "$%s" of "%s" is not supported as a command option. Only "%s" types are allowed.', $this->memberName, $this->name, $this->sourceName, \implode('", "', self::ALLOWED_UNION_TYPES)));
        }
        if (\false !== $this->default) {
            throw new LogicException(\sprintf('The option %s "$%s" of "%s" must have a default value of false.', $this->memberName, $this->name, $this->sourceName));
        }
        $this->mode = InputOption::VALUE_OPTIONAL;
        return $this;
    }
}
