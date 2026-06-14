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
use Lines202606\Symfony\Component\Console\Attribute\AsCommand;
use Lines202606\Symfony\Component\Console\Completion\CompletionInput;
use Lines202606\Symfony\Component\Console\Completion\CompletionSuggestions;
use Lines202606\Symfony\Component\Console\Completion\Suggestion;
use Lines202606\Symfony\Component\Console\Exception\ExceptionInterface;
use Lines202606\Symfony\Component\Console\Exception\InvalidArgumentException;
use Lines202606\Symfony\Component\Console\Exception\LogicException;
use Lines202606\Symfony\Component\Console\Helper\HelperInterface;
use Lines202606\Symfony\Component\Console\Helper\HelperSet;
use Lines202606\Symfony\Component\Console\Input\InputArgument;
use Lines202606\Symfony\Component\Console\Input\InputDefinition;
use Lines202606\Symfony\Component\Console\Input\InputInterface;
use Lines202606\Symfony\Component\Console\Input\InputOption;
use Lines202606\Symfony\Component\Console\Output\OutputInterface;
/**
 * Base class for all commands.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class Command implements SignalableCommandInterface
{
    // see https://tldp.org/LDP/abs/html/exitcodes.html
    public const SUCCESS = 0;
    public const FAILURE = 1;
    public const INVALID = 2;
    /**
     * @var \Symfony\Component\Console\Application|null
     */
    private $application;
    /**
     * @var string|null
     */
    private $name;
    /**
     * @var string|null
     */
    private $processTitle;
    /**
     * @var mixed[]
     */
    private $aliases = [];
    /**
     * @var \Symfony\Component\Console\Input\InputDefinition
     */
    private $definition;
    /**
     * @var bool
     */
    private $hidden = \false;
    /**
     * @var string
     */
    private $help = '';
    /**
     * @var string
     */
    private $description = '';
    /**
     * @var \Symfony\Component\Console\Input\InputDefinition|null
     */
    private $fullDefinition;
    /**
     * @var bool
     */
    private $ignoreValidationErrors = \false;
    /**
     * @var \Symfony\Component\Console\Command\InvokableCommand|null
     */
    private $code;
    /**
     * @var mixed[]
     */
    private $synopsis = [];
    /**
     * @var mixed[]
     */
    private $usages = [];
    /**
     * @var \Symfony\Component\Console\Helper\HelperSet|null
     */
    private $helperSet;
    /**
     * @param string|null $name The name of the command; passing null means it must be set in configure()
     *
     * @throws LogicException When the command name is empty
     */
    public function __construct(?string $name = null, ?callable $code = null)
    {
        $this->definition = new InputDefinition();
        $attribute = $this->getCommandAttribute($code);
        if ($code) {
            $this->setCode($code);
        }
        if (null !== ($name = $name ?? (($nullsafeVariable14 = $attribute) ? $nullsafeVariable14->name : null))) {
            $aliases = \explode('|', $name);
            if ('' === ($name = \array_shift($aliases))) {
                $this->setHidden(\true);
                $name = \array_shift($aliases);
            }
            // we must not overwrite existing aliases, combine new ones with existing ones
            $aliases = \array_unique(\array_merge($this->aliases, $aliases));
            $this->setAliases($aliases);
        }
        if (null !== $name) {
            $this->setName($name);
        }
        if ('' === $this->description) {
            $this->setDescription((($nullsafeVariable1 = $attribute) ? $nullsafeVariable1->description : null) ?? '');
        }
        if ('' === $this->help) {
            $this->setHelp((($nullsafeVariable2 = $attribute) ? $nullsafeVariable2->help : null) ?? '');
        }
        foreach ((($nullsafeVariable3 = $attribute) ? $nullsafeVariable3->usages : null) ?? [] as $usage) {
            $this->addUsage($usage);
        }
        if (!$code && \is_callable($this) && self::class === (new \ReflectionMethod($this, 'execute'))->class) {
            $this->code = new InvokableCommand($this, \Closure::fromCallable($this));
        }
        $this->configure();
    }
    /**
     * Ignores validation errors.
     *
     * This is mainly useful for the help command.
     */
    public function ignoreValidationErrors() : void
    {
        $this->ignoreValidationErrors = \true;
    }
    public function setApplication(?Application $application) : void
    {
        $this->application = $application;
        if ($application) {
            $this->setHelperSet($application->getHelperSet());
        } else {
            $this->helperSet = null;
        }
        $this->fullDefinition = null;
    }
    public function setHelperSet(HelperSet $helperSet) : void
    {
        $this->helperSet = $helperSet;
    }
    /**
     * Gets the helper set.
     */
    public function getHelperSet() : ?HelperSet
    {
        return $this->helperSet;
    }
    /**
     * Gets the application instance for this command.
     */
    public function getApplication() : ?Application
    {
        return $this->application;
    }
    /**
     * Checks whether the command is enabled or not in the current environment.
     *
     * Override this to check for x or y and return false if the command cannot
     * run properly under the current conditions.
     */
    public function isEnabled() : bool
    {
        return \true;
    }
    /**
     * Configures the current command.
     */
    protected function configure() : void
    {
    }
    /**
     * Executes the current command.
     *
     * This method is not abstract because you can use this class
     * as a concrete class. In this case, instead of defining the
     * execute() method, you set the code to execute by passing
     * a Closure to the setCode() method.
     *
     * @return int 0 if everything went fine, or an exit code
     *
     * @throws LogicException When this abstract method is not implemented
     *
     * @see setCode()
     */
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        throw new LogicException('You must override the execute() method in the concrete command class.');
    }
    /**
     * Interacts with the user.
     *
     * This method is executed before the InputDefinition is validated.
     * This means that this is the only place where the command can
     * interactively ask for values of missing required arguments.
     */
    protected function interact(InputInterface $input, OutputInterface $output) : void
    {
    }
    /**
     * Initializes the command after the input has been bound and before the input
     * is validated.
     *
     * This is mainly useful when a lot of commands extends one main command
     * where some things need to be initialized based on the input arguments and options.
     *
     * @see InputInterface::bind()
     * @see InputInterface::validate()
     */
    protected function initialize(InputInterface $input, OutputInterface $output) : void
    {
    }
    /**
     * Runs the command.
     *
     * The code to execute is either defined directly with the
     * setCode() method or by overriding the execute() method
     * in a sub-class.
     *
     * @return int The command exit code
     *
     * @throws ExceptionInterface When input binding fails. Bypass this by calling {@link ignoreValidationErrors()}.
     *
     * @see setCode()
     * @see execute()
     */
    public function run(InputInterface $input, OutputInterface $output) : int
    {
        // add the application arguments and options
        $this->mergeApplicationDefinition();
        // bind the input against the command specific arguments/options
        try {
            $input->bind($this->getDefinition());
        } catch (ExceptionInterface $e) {
            if (!$this->ignoreValidationErrors) {
                throw $e;
            }
        }
        $this->initialize($input, $output);
        if (null !== $this->processTitle) {
            if (\function_exists('cli_set_process_title')) {
                if (!@\cli_set_process_title($this->processTitle)) {
                    if ('Darwin' === \PHP_OS) {
                        $output->writeln('<comment>Running "cli_set_process_title" as an unprivileged user is not supported on MacOS.</comment>', OutputInterface::VERBOSITY_VERY_VERBOSE);
                    } else {
                        \cli_set_process_title($this->processTitle);
                    }
                }
            } elseif (\function_exists('Lines202606\\setproctitle')) {
                setproctitle($this->processTitle);
            } elseif (OutputInterface::VERBOSITY_VERY_VERBOSE === $output->getVerbosity()) {
                $output->writeln('<comment>Install the proctitle PECL to be able to change the process title.</comment>');
            }
        }
        if ($input->isInteractive()) {
            $this->interact($input, $output);
            if (($nullsafeVariable4 = $this->code) ? $nullsafeVariable4->isInteractive() : null) {
                $this->code->interact($input, $output);
            }
        }
        // The command name argument is often omitted when a command is executed directly with its run() method.
        // It would fail the validation if we didn't make sure the command argument is present,
        // since it's required by the application.
        if ($input->hasArgument('command') && null === $input->getArgument('command')) {
            $input->setArgument('command', $this->getName());
        }
        $input->validate();
        if ($this->code) {
            return ($this->code)($input, $output);
        }
        return $this->execute($input, $output);
    }
    /**
     * Supplies suggestions when resolving possible completion options for input (e.g. option or argument).
     */
    public function complete(CompletionInput $input, CompletionSuggestions $suggestions) : void
    {
        $definition = $this->getDefinition();
        if (CompletionInput::TYPE_OPTION_VALUE === $input->getCompletionType() && $definition->hasOption($input->getCompletionName())) {
            $definition->getOption($input->getCompletionName())->complete($input, $suggestions);
        } elseif (CompletionInput::TYPE_ARGUMENT_VALUE === $input->getCompletionType() && $definition->hasArgument($input->getCompletionName())) {
            $definition->getArgument($input->getCompletionName())->complete($input, $suggestions);
        }
    }
    /**
     * Gets the code that is executed by the command.
     *
     * @return ?callable null if the code has not been set with setCode()
     */
    public function getCode() : ?callable
    {
        return ($nullsafeVariable5 = $this->code) ? $nullsafeVariable5->getCode() : null;
    }
    /**
     * Sets the code to execute when running this command.
     *
     * If this method is used, it overrides the code defined
     * in the execute() method.
     *
     * @param callable $code A callable(InputInterface $input, OutputInterface $output)
     *
     * @return $this
     *
     * @throws InvalidArgumentException
     *
     * @see execute()
     */
    public function setCode(callable $code)
    {
        $this->code = new InvokableCommand($this, $code);
        return $this;
    }
    /**
     * Merges the application definition with the command definition.
     *
     * This method is not part of public API and should not be used directly.
     *
     * @param bool $mergeArgs Whether to merge or not the Application definition arguments to Command definition arguments
     *
     * @internal
     */
    public function mergeApplicationDefinition(bool $mergeArgs = \true) : void
    {
        if (null === $this->application) {
            return;
        }
        $this->fullDefinition = new InputDefinition();
        $this->fullDefinition->setOptions($this->definition->getOptions());
        $this->fullDefinition->addOptions($this->application->getDefinition()->getOptions());
        if ($mergeArgs) {
            $this->fullDefinition->setArguments($this->application->getDefinition()->getArguments());
            $this->fullDefinition->addArguments($this->definition->getArguments());
        } else {
            $this->fullDefinition->setArguments($this->definition->getArguments());
        }
    }
    /**
     * Sets an array of argument and option instances.
     *
     * @return $this
     * @param mixed[]|\Symfony\Component\Console\Input\InputDefinition $definition
     */
    public function setDefinition($definition)
    {
        if ($definition instanceof InputDefinition) {
            $this->definition = $definition;
        } else {
            $this->definition->setDefinition($definition);
        }
        $this->fullDefinition = null;
        return $this;
    }
    /**
     * Gets the InputDefinition attached to this Command.
     */
    public function getDefinition() : InputDefinition
    {
        return $this->fullDefinition ?? $this->getNativeDefinition();
    }
    /**
     * Gets the InputDefinition to be used to create representations of this Command.
     *
     * Can be overridden to provide the original command representation when it would otherwise
     * be changed by merging with the application InputDefinition.
     *
     * This method is not part of public API and should not be used directly.
     */
    public function getNativeDefinition() : InputDefinition
    {
        if ($this->definition === null) {
            throw new LogicException(\sprintf('Command class "%s" is not correctly initialized. You probably forgot to call the parent constructor.', static::class));
        }
        $definition = $this->definition;
        if ($this->code && !$definition->getArguments() && !$definition->getOptions()) {
            $this->code->configure($definition);
        }
        return $definition;
    }
    /**
     * Adds an argument.
     *
     * @param                                                                               $mode            The argument mode: InputArgument::REQUIRED or InputArgument::OPTIONAL
     * @param                                                                               $default         The default value (for InputArgument::OPTIONAL mode only)
     * @param array|\Closure(CompletionInput,CompletionSuggestions):list<string|Suggestion> $suggestedValues The values used for input completion
     *
     * @return $this
     *
     * @throws InvalidArgumentException When argument mode is not valid
     * @param mixed $default
     */
    public function addArgument(string $name, ?int $mode = null, string $description = '', $default = null, $suggestedValues = [])
    {
        $this->definition->addArgument(new InputArgument($name, $mode, $description, $default, $suggestedValues));
        ($nullsafeVariable6 = $this->fullDefinition) ? $nullsafeVariable6->addArgument(new InputArgument($name, $mode, $description, $default, $suggestedValues)) : null;
        return $this;
    }
    /**
     * Adds an option.
     *
     * @param                                                                               $shortcut        The shortcuts, can be null, a string of shortcuts delimited by | or an array of shortcuts
     * @param                                                                               $mode            The option mode: One of the InputOption::VALUE_* constants
     * @param                                                                               $default         The default value (must be null for InputOption::VALUE_NONE)
     * @param array|\Closure(CompletionInput,CompletionSuggestions):list<string|Suggestion> $suggestedValues The values used for input completion
     *
     * @return $this
     *
     * @throws InvalidArgumentException If option mode is invalid or incompatible
     * @param string|mixed[]|null $shortcut
     * @param mixed $default
     */
    public function addOption(string $name, $shortcut = null, ?int $mode = null, string $description = '', $default = null, $suggestedValues = [])
    {
        $this->definition->addOption(new InputOption($name, $shortcut, $mode, $description, $default, $suggestedValues));
        ($nullsafeVariable7 = $this->fullDefinition) ? $nullsafeVariable7->addOption(new InputOption($name, $shortcut, $mode, $description, $default, $suggestedValues)) : null;
        return $this;
    }
    /**
     * Sets the name of the command.
     *
     * This method can set both the namespace and the name if
     * you separate them by a colon (:)
     *
     *     $command->setName('foo:bar');
     *
     * @return $this
     *
     * @throws InvalidArgumentException When the name is invalid
     */
    public function setName(string $name)
    {
        $this->validateName($name);
        $this->name = $name;
        return $this;
    }
    /**
     * Sets the process title of the command.
     *
     * This feature should be used only when creating a long process command,
     * like a daemon.
     *
     * @return $this
     */
    public function setProcessTitle(string $title)
    {
        $this->processTitle = $title;
        return $this;
    }
    /**
     * Returns the command name.
     */
    public function getName() : ?string
    {
        return $this->name;
    }
    /**
     * @param bool $hidden Whether or not the command should be hidden from the list of commands
     *
     * @return $this
     */
    public function setHidden(bool $hidden = \true)
    {
        $this->hidden = $hidden;
        return $this;
    }
    /**
     * @return bool whether the command should be publicly shown or not
     */
    public function isHidden() : bool
    {
        return $this->hidden;
    }
    /**
     * Sets the description for the command.
     *
     * @return $this
     */
    public function setDescription(string $description)
    {
        $this->description = $description;
        return $this;
    }
    /**
     * Returns the description for the command.
     */
    public function getDescription() : string
    {
        return $this->description;
    }
    /**
     * Sets the help for the command.
     *
     * @return $this
     */
    public function setHelp(string $help)
    {
        $this->help = $help;
        return $this;
    }
    /**
     * Returns the help for the command.
     */
    public function getHelp() : string
    {
        return $this->help;
    }
    /**
     * Returns the processed help for the command replacing the %command.name% and
     * %command.full_name% patterns with the real values dynamically.
     */
    public function getProcessedHelp() : string
    {
        $name = $this->name;
        $isSingleCommand = ($nullsafeVariable8 = $this->application) ? $nullsafeVariable8->isSingleCommand() : null;
        $placeholders = ['%command.name%', '%command.full_name%'];
        $replacements = [$name, $isSingleCommand ? $_SERVER['PHP_SELF'] : $_SERVER['PHP_SELF'] . ' ' . $name];
        return \str_replace($placeholders, $replacements, $this->getHelp() ?: $this->getDescription());
    }
    /**
     * Sets the aliases for the command.
     *
     * @param string[] $aliases An array of aliases for the command
     *
     * @return $this
     *
     * @throws InvalidArgumentException When an alias is invalid
     */
    public function setAliases(iterable $aliases)
    {
        $list = [];
        foreach ($aliases as $alias) {
            $this->validateName($alias);
            $list[] = $alias;
        }
        $this->aliases = $list;
        return $this;
    }
    /**
     * Returns the aliases for the command.
     */
    public function getAliases() : array
    {
        return $this->aliases;
    }
    /**
     * Returns the synopsis for the command.
     *
     * @param bool $short Whether to show the short version of the synopsis (with options folded) or not
     */
    public function getSynopsis(bool $short = \false) : string
    {
        $key = $short ? 'short' : 'long';
        if (!isset($this->synopsis[$key])) {
            $this->synopsis[$key] = \trim(\sprintf('%s %s', $this->name, $this->definition->getSynopsis($short)));
        }
        return $this->synopsis[$key];
    }
    /**
     * Add a command usage example, it'll be prefixed with the command name.
     *
     * @return $this
     */
    public function addUsage(string $usage)
    {
        if (\strncmp($usage, $this->name, \strlen($this->name)) !== 0) {
            $usage = \sprintf('%s %s', $this->name, $usage);
        }
        $this->usages[] = $usage;
        return $this;
    }
    /**
     * Returns alternative usages of the command.
     */
    public function getUsages() : array
    {
        return $this->usages;
    }
    /**
     * Gets a helper instance by name.
     *
     * @throws LogicException           if no HelperSet is defined
     * @throws InvalidArgumentException if the helper is not defined
     */
    public function getHelper(string $name) : HelperInterface
    {
        if (null === $this->helperSet) {
            throw new LogicException(\sprintf('Cannot retrieve helper "%s" because there is no HelperSet defined. Did you forget to add your command to the application or to set the application on the command using the setApplication() method? You can also set the HelperSet directly using the setHelperSet() method.', $name));
        }
        return $this->helperSet->get($name);
    }
    public function getSubscribedSignals() : array
    {
        return (($nullsafeVariable9 = $this->code) ? $nullsafeVariable9->getSubscribedSignals() : null) ?? [];
    }
    /**
     * @return int|false
     * @param int|false $previousExitCode
     */
    public function handleSignal(int $signal, $previousExitCode = 0)
    {
        return (($nullsafeVariable10 = $this->code) ? $nullsafeVariable10->handleSignal($signal, $previousExitCode) : null) ?? \false;
    }
    /**
     * Validates a command name.
     *
     * It must be non-empty and parts can optionally be separated by ":".
     *
     * @throws InvalidArgumentException When the name is invalid
     */
    private function validateName(string $name) : void
    {
        if (!\preg_match('/^[^\\:]++(\\:[^\\:]++)*$/', $name)) {
            throw new InvalidArgumentException(\sprintf('Command name "%s" is invalid.', $name));
        }
    }
    private function getCommandAttribute(?callable $code) : ?AsCommand
    {
        if (null === $code) {
            /** @var AsCommand|null $attribute */
            $attribute = ($nullsafeVariable11 = (\method_exists(new \ReflectionClass(static::class), 'getAttributes') ? (new \ReflectionClass(static::class))->getAttributes(AsCommand::class) : [])[0] ?? null) ? $nullsafeVariable11->newInstance() : null;
            return $attribute;
        }
        $reflection = new \ReflectionFunction(\Closure::fromCallable($code));
        if ($reflection->isAnonymous() || !($class = $reflection->getClosureScopeClass())) {
            throw new InvalidArgumentException(\sprintf('The command must be an instance of "%s", an invokable object or a method of an object.', self::class));
        }
        /** @var AsCommand|null $attribute */
        $attribute = ($nullsafeVariable12 = (\method_exists($reflection, 'getAttributes') ? $reflection->getAttributes(AsCommand::class) : [])[0] ?? null) ? $nullsafeVariable12->newInstance() : null;
        if (!$attribute && '__invoke' === $reflection->getName()) {
            /** @var AsCommand|null $attribute */
            $attribute = ($nullsafeVariable13 = (\method_exists($class, 'getAttributes') ? $class->getAttributes(AsCommand::class) : [])[0] ?? null) ? $nullsafeVariable13->newInstance() : null;
        }
        if (!$attribute) {
            throw new LogicException(\sprintf('The command must use the "%s" attribute.', AsCommand::class));
        }
        return $attribute;
    }
}
