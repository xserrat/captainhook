<?php

/**
 * This file is part of CaptainHook
 *
 * (c) Sebastian Feldmann <sf@sebastian-feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace CaptainHook\App\Hook\Template\Local;

use CaptainHook\App\CH;
use CaptainHook\App\Hook\Template;
use CaptainHook\App\Hooks;
use SebastianFeldmann\Camino\Path;
use SebastianFeldmann\Camino\Path\Directory;
use Symfony\Component\Process\PhpExecutableFinder;

/**
 * Shell class
 *
 * Generates the sourcecode for the php hook scripts in .git/hooks/*.
 *
 * @package CaptainHook
 * @author  Sebastian Feldmann <sf@sebastian-feldmann.info>
 * @link    https://github.com/captainhookphp/captainhook
 * @since   Class available since Release 5.0.0
 */
class Shell extends Template\Local
{
    /**
     * Returns lines of code for the local src installation
     *
     * @param  string $hook
     * @return array<string>
     */
    protected function getHookLines(string $hook): array
    {
        $useStdIn = ' <&0';
        $useTTY   = [];

        if (Hooks::allowsUserInput($hook)) {
            $useStdIn = '';
            $useTTY   = [
                'if [ -t 1 ]; then',
                '    # If we\'re in a terminal, redirect stdout and stderr to /dev/tty and',
                '    # read stdin from /dev/tty. Allow interactive mode for CaptainHook.',
                '    exec >/dev/tty 2>/dev/tty </dev/tty',
                '    INTERACTIVE=""',
                'fi',
            ];
        }

        return array_merge(
            [
                '#!/bin/sh',
                '',
                '# installed by CaptainHook ' . CH::VERSION,
                '',
                'INTERACTIVE="--no-interaction"',
            ],
            $useTTY,
            [
                '',
                $this->getExecutable()
                    . ' $INTERACTIVE'
                    . ' --configuration=' . $this->pathInfo->getConfigPath()
                    . ' --bootstrap=' . $this->config->getBootstrap()
                    . ' hook:' . $hook . ' "$@"' . $useStdIn,
            ]
        );
    }

    /**
     * Returns the path to the executable including a configured php executable
     *
     * @return string
     */
    private function getExecutable(): string
    {
        $executable = !empty($this->config->getPhpPath()) ? $this->config->getPhpPath() . ' ' : '';

        if (!empty($this->config->getRunPath())) {
            return $executable . $this->config->getRunPath();
        }

        return $executable . $this->pathInfo->getExecutablePath();
    }
}
