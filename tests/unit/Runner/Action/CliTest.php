<?php

/**
 * This file is part of CaptainHook
 *
 * (c) Sebastian Feldmann <sf@sebastian-feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CaptainHook\App\Runner\Action;

use CaptainHook\App\Config\Mockery as ConfigMockery;
use CaptainHook\App\Console\IO\Mockery as IOMockery;
use CaptainHook\App\Mockery as CHMockery;
use Exception;
use PHPUnit\Framework\TestCase;

class CliTest extends TestCase
{
    use ConfigMockery;
    use IOMockery;
    use CHMockery;

    /**
     * Tests Cli::execute
     *
     * @throws \CaptainHook\App\Exception\ActionFailed
     */
    public function testExecuteSuccess(): void
    {
        if (defined('PHP_WINDOWS_VERSION_MAJOR')) {
            $this->markTestSkipped('not tested on windows');
        }

        $args   = [];
        $io     = $this->createIOMock();
        $repo   = $this->createRepositoryMock();
        $config = $this->createConfigMock();
        $action = $this->createActionConfigMock();
        $cmd    = CH_PATH_FILES . '/bin/success';

        $io->method('getArguments')->willReturn($args);
        $io->expects($this->atLeast(1))->method('write');
        $action->expects($this->once())->method('getAction')->willReturn($cmd);

        $cli = new Cli();
        $cli->execute($config, $io, $repo, $action);
    }

    /**
     * Tests Cli::execute
     *
     * @throws \CaptainHook\App\Exception\ActionFailed
     */
    public function testExecuteSuccessWithReplacements(): void
    {
        if (defined('PHP_WINDOWS_VERSION_MAJOR')) {
            $this->markTestSkipped('not tested on windows');
        }

        $args   = ['file' => 'bin', 'mode' => 'success'];
        $io     = $this->createIOMock();
        $repo   = $this->createRepositoryMock();
        $config = $this->createConfigMock();
        $action = $this->createActionConfigMock();
        $cmd    = CH_PATH_FILES . '/{$FILE}/{$MODE}';

        $io->method('getArguments')->willReturn($args);
        $io->expects($this->atLeast(1))->method('write');
        $action->expects($this->once())->method('getAction')->willReturn($cmd);

        $cli = new Cli();
        $cli->execute($config, $io, $repo, $action);
    }

    /**
     * Tests Cli::execute
     *
     * @throws \CaptainHook\App\Exception\ActionFailed
     */
    public function testExecuteFailure(): void
    {
        $this->expectException(Exception::class);

        $args   = [];
        $io     = $this->createIOMock();
        $repo   = $this->createRepositoryMock();
        $config = $this->createConfigMock();
        $action = $this->createActionConfigMock();
        $cmd    = CH_PATH_FILES . '/bin/failure';

        $io->method('getArguments')->willReturn($args);
        $action->expects($this->once())->method('getAction')->willReturn($cmd);

        $cli = new Cli();
        $cli->execute($config, $io, $repo, $action);
    }
}
