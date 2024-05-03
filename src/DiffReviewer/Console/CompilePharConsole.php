<?php

/**
 * Copyright © 2016-present DiffReviewer Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace DiffReviewer\DiffReviewer\Console;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class CompilePharConsole extends AbstractDiffReviewerConsole
{
    /**
     * @return void
     */
    protected function configure(): void
    {
        $this->setName('diff-reviewer:compile')
            ->setDescription('Builds a PHAR for the DiffReviewers.');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Building argument list cache...');
        $this->executeProcess(['php', 'bin/console', 'diff-reviewer:build']);

        $output->writeln('Clean the cache...');
        $this->executeProcess(['php', 'bin/console', 'cache:clear', '-e', 'prod', '--no-debug']);

        $output->writeln('Warm up the cache...');
        $this->executeProcess(['php', 'bin/console', 'cache:warmup', '-e', 'prod', '--no-debug']);

        $output->writeln('Build the PHAR...');
        $this->executeProcess(['php', 'box.phar', 'compile', '--no-parallel'], getcwd() . '/compiler/build');

        return static::CODE_SUCCESS;
    }

    /**
     * @codeCoverageIgnore
     */
    protected function executeProcess(array $processDefinition, ?string $cwd = null, ?array $env = []): void
    {
        $process = new Process($processDefinition, $cwd);
        $process->start();

        $iterator = $process->getIterator($process::ITER_SKIP_ERR | $process::ITER_KEEP_OUTPUT);

        foreach ($iterator as $data) {
            echo $data . "\n";
        }
    }
}
