<?php

namespace BlastCloud\Hybrid\Helpers;

use BlastCloud\Hybrid\Expectation;
use PHPUnit\Runner\Extension\Facade;
use PHPUnit\Runner\Extension\ParameterCollection;
use PHPUnit\TextUI\Configuration\Configuration;

final class Extension implements \PHPUnit\Runner\Extension\Extension
{
    const NAMESPACE = 'HybridFilterNamespace';
    const MACRO_FILE = 'HybridMacroFile';

    protected $macroFiles = [];

    /**
     * Set off the setup
     *
     * @throws \Exception
     */
    public function executeBeforeFirstTest(): void
    {
        if ($namespace = $GLOBALS[self::NAMESPACE] ?? false) {
            Expectation::addNamespace($namespace);
        }

        if ($file = $GLOBALS[self::MACRO_FILE] ?? false) {
            $this->macroFiles[] = $file;
        }

        $this->loadMacros();
    }

    /**
     * Load any macros based on the file specified in the PHPUnit configs
     *
     * @throws \Exception
     */
    public function loadMacros()
    {
        foreach ($this->macroFiles as $file) {
            if (!is_file($file)) {
                throw new \Exception("The macro file {$file} cannot be found.");
            }

            if (!is_readable($file)) {
                throw new \Exception("The macro file {$file} cannot be read.");
            }

            require_once $file;
        }
    }

    public function bootstrap(Configuration $configuration, Facade $facade, ParameterCollection $parameters): void
    {
        if ($namespace = $GLOBALS[self::NAMESPACE] ?? false) {
            \BlastCloud\Hybrid\Expectation::addNamespace($namespace);
        }

        if ($file = $GLOBALS[self::MACRO_FILE] ?? false) {
            $this->macroFiles[] = $file;
        }

        $this->loadMacros();
    }
}