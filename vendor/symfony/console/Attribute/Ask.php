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
use Lines202606\Symfony\Component\Console\Exception\InvalidArgumentException;
use Lines202606\Symfony\Component\Console\Exception\LogicException;
use Lines202606\Symfony\Component\Console\Input\File\InputFile;
use Lines202606\Symfony\Component\Console\Input\InputInterface;
use Lines202606\Symfony\Component\Console\Question\ConfirmationQuestion;
use Lines202606\Symfony\Component\Console\Question\FileQuestion;
use Lines202606\Symfony\Component\Console\Question\Question;
use Lines202606\Symfony\Component\Console\Style\SymfonyStyle;
use Lines202606\Symfony\Component\Validator\Constraint;
#[\Attribute(\Attribute::TARGET_PARAMETER | \Attribute::TARGET_PROPERTY)]
class Ask implements InteractiveAttributeInterface
{
    /**
     * @var string
     */
    public $question;
    /**
     * @var string|bool|int|float|null
     */
    public $default = null;
    /**
     * @var bool
     */
    public $hidden = \false;
    /**
     * @var bool
     */
    public $multiline = \false;
    /**
     * @var bool
     */
    public $trimmable = \true;
    /**
     * @var int|null
     */
    public $timeout;
    /**
     * @var int|null
     */
    public $maxAttempts;
    /**
     * @var mixed[]
     */
    public $constraints = [];
    /**
     * @var \Closure|null
     */
    public $normalizer;
    /**
     * @var \Closure|null
     */
    public $validator;
    /**
     * @var \Closure
     */
    private $closure;
    /**
     * @param string                     $question    The question to ask the user
     * @param string|bool|int|float|null $default     The default answer to return if the user enters nothing
     * @param bool                       $hidden      Whether the user response must be hidden or not
     * @param bool                       $multiline   Whether the user response should accept newline characters
     * @param bool                       $trimmable   Whether the user response must be trimmed or not
     * @param int|null                   $timeout     The maximum time the user has to answer the question in seconds
     * @param callable|null              $validator   The validator for the question
     * @param int|null                   $maxAttempts The maximum number of attempts allowed to answer the question.
     *                                                Null means an unlimited number of attempts
     */
    public function __construct(string $question, $default = null, bool $hidden = \false, bool $multiline = \false, bool $trimmable = \true, ?int $timeout = null, ?callable $normalizer = null, ?callable $validator = null, ?int $maxAttempts = null, array $constraints = [])
    {
        $this->question = $question;
        $this->default = $default;
        $this->hidden = $hidden;
        $this->multiline = $multiline;
        $this->trimmable = $trimmable;
        $this->timeout = $timeout;
        $this->maxAttempts = $maxAttempts;
        /** @var Constraint[] */
        $this->constraints = $constraints;
        $this->normalizer = $normalizer ? \Closure::fromCallable($normalizer) : null;
        $this->validator = $validator ? \Closure::fromCallable($validator) : null;
    }
    /**
     * @internal
     * @param \ReflectionParameter|\ReflectionProperty $member
     */
    public static function tryFrom($member, string $name) : ?self
    {
        $reflection = new ReflectionMember($member);
        if (!($self = $reflection->getAttribute(self::class))) {
            return null;
        }
        $type = $reflection->getType();
        if (!$type instanceof \ReflectionNamedType) {
            throw new LogicException(\sprintf('The %s "$%s" of "%s" must have a named type. Untyped, Union or Intersection types are not supported for interactive questions.', $reflection->getMemberName(), $name, $reflection->getSourceName()));
        }
        if ($backedType::tryFrom($value) === null) {
            throw InvalidArgumentException::fromEnumValue($reflection->getName(), $value, \array_column($backedType::cases(), 'value'));
        }
        $self->closure = function (SymfonyStyle $io, InputInterface $input) use($self, $reflection, $name, $type) {
            if ($reflection->isProperty() && isset($this->{$reflection->getName()})) {
                return;
            }
            if ($reflection->isParameter() && !\in_array($input->getArgument($name), [null, []], \true)) {
                return;
            }
            $typeName = $type->getName();
            if (InputFile::class === $typeName) {
                $question = new FileQuestion($self->question);
                $question->setValidator($self->validator);
                $question->setMaxAttempts($self->maxAttempts);
                $question->setConstraints($self->constraints);
                $value = $io->askQuestion($question);
                if (null === $value && !$reflection->isNullable()) {
                    return;
                }
                if ($reflection->isProperty()) {
                    $this->{$reflection->getName()} = $value;
                } else {
                    $input->setArgument($name, $value);
                }
                return;
            }
            if ('bool' === $typeName) {
                $self->default = $self->default ?? \false;
                if (!\is_bool($self->default)) {
                    throw new LogicException(\sprintf('The "%s::$default" value for the %s "$%s" of "%s" must be a boolean.', self::class, $reflection->getMemberName(), $name, $reflection->getSourceName()));
                }
                $question = new ConfirmationQuestion($self->question, $self->default);
            } else {
                $question = new Question($self->question, $self->default);
            }
            $question->setHidden($self->hidden);
            $question->setMultiline($self->multiline);
            $question->setTrimmable($self->trimmable);
            $question->setTimeout($self->timeout);
            if (!$self->validator && $reflection->isProperty() && 'array' !== $typeName) {
                $self->validator = function ($value) use($reflection) {
                    return $this->{$reflection->getName()} = $value;
                };
            }
            $question->setValidator($self->validator);
            $question->setMaxAttempts($self->maxAttempts);
            $question->setConstraints($self->constraints);
            if ($self->normalizer) {
                $question->setNormalizer($self->normalizer);
            } elseif (\is_subclass_of($typeName, \BackedEnum::class)) {
                /** @var class-string<\BackedEnum> $backedType */
                $backedType = $reflection->getType()->getName();
                $question->setNormalizer(static function ($value) use($backedType) {
                    return $backedType::tryFrom($value);
                });
            }
            if ('array' === $typeName) {
                $value = [];
                while ($v = $io->askQuestion($question)) {
                    if ("\x04" === $v || \PHP_EOL === $v || $question->isTrimmable() && '' === ($v = \trim($v))) {
                        break;
                    }
                    $value[] = $v;
                }
            } else {
                $value = $io->askQuestion($question);
            }
            if (null === $value && !$reflection->isNullable()) {
                return;
            }
            if ($reflection->isProperty()) {
                $this->{$reflection->getName()} = $value;
            } else {
                $input->setArgument($name, $value);
            }
        };
        return $self;
    }
    /**
     * @internal
     */
    public function getFunction(object $instance) : \ReflectionFunction
    {
        return new \ReflectionFunction($this->closure->bindTo($instance, \get_class($instance)));
    }
}
