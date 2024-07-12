<?php

declare (strict_types=1);
namespace Lines202407\Termwind\Components;

use Lines202407\Symfony\Component\Console\Output\OutputInterface;
use Lines202407\Termwind\Actions\StyleToMethod;
use Lines202407\Termwind\Html\InheritStyles;
use Lines202407\Termwind\ValueObjects\Styles;
/**
 * @internal
 *
 * @method Element inheritFromStyles(Styles $styles)
 * @method Element fontBold()
 * @method Element strong()
 * @method Element italic()
 * @method Element underline()
 * @method Element lineThrough()
 * @method int getLength()
 * @method int getInnerWidth()
 * @method array getProperties()
 * @method Element href(string $href)
 * @method bool hasStyle(string $style)
 * @method Element addStyle(string $style)
 */
abstract class Element
{
    /**
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    protected $output;
    /**
     * @var array<int, (Element | string)>|string
     */
    protected $content;
    /** @var string[] */
    protected static $defaultStyles = [];
    /**
     * @var \Termwind\ValueObjects\Styles
     */
    protected $styles;
    /**
     * Creates an element instance.
     *
     * @param  array<int, Element|string>|string  $content
     */
    public final function __construct(OutputInterface $output, $content, ?\Lines202407\Termwind\ValueObjects\Styles $styles = null)
    {
        $this->output = $output;
        $this->content = $content;
        $this->styles = $styles ?? new Styles([[], [], \false], [], [], static::$defaultStyles);
        $this->styles->setElement($this);
    }
    /**
     * Creates an element instance with the given styles.
     *
     * @param  array<int, Element|string>|string  $content
     * @param  array<string, mixed>  $properties
     * @return static
     */
    public static final function fromStyles(OutputInterface $output, $content, string $styles = '', array $properties = [])
    {
        $element = new static($output, $content);
        if ($properties !== []) {
            $element->styles->setProperties($properties);
        }
        $elementStyles = StyleToMethod::multiple($element->styles, $styles);
        return new static($output, $content, $elementStyles);
    }
    /**
     * Get the string representation of the element.
     */
    public function toString() : string
    {
        if (\is_array($this->content)) {
            $inheritance = new InheritStyles();
            $this->content = \implode('', $inheritance($this->content, $this->styles));
        }
        return $this->styles->format($this->content);
    }
    /**
     * @param  array<int, mixed>  $arguments
     * @return mixed
     */
    public function __call(string $name, array $arguments)
    {
        if (\method_exists($this->styles, $name)) {
            $result = $this->styles->{$name}(...$arguments);
            if (\strncmp($name, 'get', \strlen('get')) === 0 || \strncmp($name, 'has', \strlen('has')) === 0) {
                return $result;
            }
        }
        return $this;
    }
    /**
     * Sets the content of the element.
     *
     * @param  array<int, Element|string>|string  $content
     * @return static
     */
    public final function setContent($content)
    {
        return new static($this->output, $content, $this->styles);
    }
    /**
     * Renders the string representation of the element on the output.
     */
    public final function render(int $options) : void
    {
        $this->output->writeln($this->toString(), $options);
    }
    /**
     * Get the string representation of the element.
     */
    public final function __toString() : string
    {
        return $this->toString();
    }
}
