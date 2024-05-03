<?php

/**
 * Copyright © 2016-present DiffReviewer Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace DiffReviewer\DiffReviewer\Console;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DiffReviewerBuildConsole extends AbstractDiffReviewerConsole
{
    /**
     * @return void
     */
    protected function configure(): void
    {
        $this->setName('diff-reviewer:build')
            ->setDescription('Builds a cache for all possible DiffReviewer arguments. This command must only be used if a new argument was supplied.');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Getting all DiffReviewer definitions...');
        $sprykDefinitions = $this->getFacade()->getDiffReviewerDefinitions();

        $output->writeln(sprintf('Found "%s" DiffReviewer definitions.', count($sprykDefinitions)));

        $output->writeln('Generating argument list ...');
        $this->getFacade()->generateArgumentList($sprykDefinitions);
        $output->writeln('Argument list has been generated.');

        return static::CODE_SUCCESS;
    }
}
