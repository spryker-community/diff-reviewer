<?php

/**
 * Copyright © 2016-present DiffReviewer Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace DiffReviewer\DiffReviewer\Console;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ChangelogConsole extends AbstractDiffReviewerConsole
{
    /**
     * @return void
     */
    protected function configure(): void
    {
        $this->setName('diff-reviewer:change-log')
            ->setDescription('Creates a changelog that can be used in GitHub PR\'s.');


        // Args

        // Branch name
        // path-to-repo
        // PR link
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Creating changelog...');

        $changelog = $this->getFacade()->generateChangelog();

        $output->writeln($changelog);

        return static::CODE_SUCCESS;
    }
}
