<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Lines202606\Symfony\Component\Console\Tester;

use Lines202606\Symfony\Component\Console\Output\TestOutput;
/**
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
final class ExecutionResult
{
    /**
     * @readonly
     * @var string
     */
    public $input;
    /**
     * @readonly
     * @var int
     */
    public $statusCode;
    /**
     * @readonly
     * @var \Symfony\Component\Console\Output\TestOutput
     */
    private $output;
    /**
     * @var array<\Closure(string):string>
     * @readonly
     */
    private $normalizers = [];
    // This is purely for memoizing purposes
    /**
     * @var mixed[]
     */
    private $results = [];
    /**
     * @param array<\Closure(string): string> $normalizers
     */
    public function __construct(string $input, int $statusCode, TestOutput $output, array $normalizers = [])
    {
        $this->input = $input;
        $this->statusCode = $statusCode;
        $this->output = $output;
        $this->normalizers = $normalizers;
    }
    /**
     * Gets the display returned by the execution of the command or application. The display combines what was
     * written on both the output and error output.
     */
    public function getDisplay(bool $normalize = \true) : string
    {
        return $this->results['display'][$normalize] = $this->results['display'][$normalize] ?? $this->normalize($this->output->getDisplayContents(), $normalize);
    }
    /**
     * Gets the output written to the output by the command or application.
     */
    public function getOutput(bool $normalize = \false) : string
    {
        return $this->results['output'][$normalize] = $this->results['output'][$normalize] ?? $this->normalize($this->output->getOutputContents(), $normalize);
    }
    /**
     * Gets the output written to the error output by the command or application.
     */
    public function getErrorOutput(bool $normalize = \false) : string
    {
        return $this->results['errorOutput'][$normalize] = $this->results['errorOutput'][$normalize] ?? $this->normalize($this->output->getErrorOutputContents(), $normalize);
    }
    /**
     * @return $this
     */
    public function dump()
    {
        $summary = "CLI: {$this->input}, Status: {$this->statusCode}";
        $output = [$summary, $this->getOutput(\true), $this->getErrorOutput(\true), $summary];
        \call_user_func(\function_exists('Lines202606\\dump') ? 'dump' : 'var_dump', \implode("\n\n", \array_filter($output)));
        return $this;
    }
    /**
     * @return never
     */
    public function dd()
    {
        $this->dump();
        exit(1);
    }
    private function normalize(string $value, bool $normalize) : string
    {
        if (!$normalize) {
            return $value;
        }
        foreach ($this->normalizers as $normalizer) {
            $value = $normalizer($value);
        }
        return \str_replace(\PHP_EOL, "\n", $value);
    }
}
