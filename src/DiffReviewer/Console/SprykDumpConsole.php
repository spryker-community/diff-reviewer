<?php

/**
 * Copyright © 2016-present DiffReviewer Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace DiffReviewer\DiffReviewer\Console;

use Exception;
use DiffReviewer\DiffReviewer\DiffReviewerConfig;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DiffReviewerDumpConsole extends AbstractDiffReviewerConsole
{
    /**
     * @var string
     */
    protected const COMMAND_NAME = 'diff-reviewer:dump';

    /**
     * @var string
     */
    protected const COMMAND_DESCRIPTION = 'Dumps a list of all DiffReviewer definitions.';

    /**
     * @var string
     */
    public const ARGUMENT_SPRYK = 'diff-reviewer';

    /**
     * @var string
     */
    protected const OPTION_LEVEL = 'level';

    /**
     * @var string
     */
    protected const OPTION_LEVEL_SHORT = 'l';

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this->setName(static::COMMAND_NAME)
            ->setDescription(static::COMMAND_DESCRIPTION)
            ->addArgument(static::ARGUMENT_SPRYK, InputOption::VALUE_OPTIONAL, 'Name of a specific DiffReviewer for which the options should be dumped.')
            ->addOption(
                static::OPTION_LEVEL,
                static::OPTION_LEVEL_SHORT,
                InputOption::VALUE_REQUIRED,
                'DiffReviewer visibility level (1, 2, 3, all). By default = 1 (main diff-reviewer commands).',
                (string)DiffReviewerConfig::SPRYK_DEFAULT_DUMP_LEVEL,
            );
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $level = $this->getLevelOption($input);

        $sprykName = current((array)$input->getArgument(static::ARGUMENT_SPRYK));
        if ($sprykName !== false) {
            $this->dumpDiffReviewer($output, $sprykName);

            return static::CODE_SUCCESS;
        }

        $this->dumpAllDiffReviewers($output, $level);

        return static::CODE_SUCCESS;
    }

    protected function getLevelOption(InputInterface $input): ?int
    {
        $level = current((array)$input->getOption(static::OPTION_LEVEL));

        return $level === 'all' ? null : (int)$level;
    }

    protected function dumpAllDiffReviewers(OutputInterface $output, ?int $level): void
    {
        $sprykDefinitions = $this->getFacade()->getDiffReviewerDefinitions($level);
        $sprykDefinitions = $this->formatDiffReviewers($sprykDefinitions);

        $output->writeln('List of DiffReviewer definitions:');
        $this->printTable($output, ['DiffReviewer name', 'Description', 'Arguments'], $sprykDefinitions);
    }

    protected function dumpDiffReviewer(OutputInterface $output, string $sprykName): void
    {
        $sprykDefinition = $this->getFacade()->getDiffReviewerDefinition($sprykName);

        $output->writeln(sprintf('<fg=green>Description of the <fg=yellow>%s</> DiffReviewer</>', $sprykName));
        $this->printTableWithRequiredArguments($sprykDefinition[DiffReviewerConfig::SPRYK_DEFINITION_KEY_ARGUMENTS], $output);
        $this->printTableWithOptionalArguments($sprykDefinition[DiffReviewerConfig::SPRYK_DEFINITION_KEY_ARGUMENTS], $output);
        $this->printTableWithPreDiffReviewers($sprykDefinition['preDiffReviewers'] ?? [], $output);
        $this->printTableWithPostDiffReviewers($sprykDefinition['postDiffReviewers'] ?? [], $output);
        $this->printCommandRunExample($sprykDefinition[DiffReviewerConfig::SPRYK_DEFINITION_KEY_ARGUMENTS], $sprykName, $output);
    }

    protected function printTableWithRequiredArguments(array $sprykArguments, OutputInterface $output): void
    {
        $output->writeln('');

        $headers = ['Required Argument', 'Description'];
        $rows = [];

        foreach ($sprykArguments as $sprykArgumentName => $sprykArgumentDefinition) {
            if (isset($sprykArgumentDefinition['value'])) {
                continue;
            }
            if (isset($sprykArgumentDefinition['default'])) {
                continue;
            }
            $rows[] = [$sprykArgumentName, $sprykArgumentDefinition['description'] ?? 'No description provided'];
        }

        if (count($rows)) {
            $output->writeln('<fg=yellow>The following arguments are required and you need to pass them</>');
            $this->printTable($output, $headers, $rows);

            return;
        }

        $output->writeln('<fg=yellow>This DiffReviewer does not have any required arguments to be passed</>');
    }

    protected function printTableWithOptionalArguments(array $sprykArguments, OutputInterface $output): void
    {
        $headers = ['Optional Argument', 'Description', 'Default', 'Value'];
        $rows = [];

        foreach ($sprykArguments as $sprykArgumentName => $sprykArgumentDefinition) {
            if (!isset($sprykArgumentDefinition['value']) && !isset($sprykArgumentDefinition['default'])) {
                continue;
            }

            $value = '';

            if (isset($sprykArgumentDefinition['value'])) {
                $value = is_array($sprykArgumentDefinition['value']) ? 'Is an array check DiffReviewer definition' : substr($sprykArgumentDefinition['value'], 0, 100);
            }

            $rows[] = [
                $sprykArgumentName,
                isset($sprykArgumentDefinition['description']) ? substr($sprykArgumentDefinition['description'], 0, 100) : '',
                $value,
                isset($sprykArgumentDefinition['default']) ? substr($sprykArgumentDefinition['default'], 0, 100) : '',
            ];
        }

        if (count($rows)) {
            $output->writeln('');
            $output->writeln('<fg=yellow>The following arguments are optional and you can pass them when needed</>');
            $this->printTable($output, $headers, $rows);
        }
    }

    protected function printTableWithPreDiffReviewers(array $preDiffReviewers, OutputInterface $output): void
    {
        $output->writeln('');

        $headers = ['PreDiffReviewers'];
        $rows = [];

        foreach ($preDiffReviewers as $preDiffReviewer) {
            if (is_array($preDiffReviewer)) {
                $preDiffReviewer = array_key_first($preDiffReviewer);
            }

            $rows[] = [
                $preDiffReviewer,
            ];
        }
        if (count($rows)) {
            $output->writeln('<fg=yellow>Pre DiffReviewers which are executed before the DiffReviewer is running</>');
            $this->printTable($output, $headers, $rows);

            return;
        }

        $output->writeln('<fg=yellow>This DiffReviewer does not have any preDiffReviewer to be executed</>');
    }

    protected function printTableWithPostDiffReviewers(array $postDiffReviewers, OutputInterface $output): void
    {
        $output->writeln('');

        $headers = ['PostDiffReviewers'];
        $rows = [];

        foreach ($postDiffReviewers as $postDiffReviewer) {
            if (is_array($postDiffReviewer)) {
                $postDiffReviewer = array_key_first($postDiffReviewer);
            }

            $rows[] = [
                $postDiffReviewer,
            ];
        }
        if (count($rows)) {
            $output->writeln('<fg=yellow>Post DiffReviewers which are executed after the DiffReviewer was running</>');
            $this->printTable($output, $headers, $rows);

            return;
        }

        $output->writeln('<fg=yellow>This DiffReviewer does not have any postDiffReviewer to be executed</>');
    }

    protected function printTable(OutputInterface $output, array $headers, array $rows): void
    {
        (new Table($output))
            ->setHeaders($headers)
            ->setRows($rows)
            ->render();
    }

    protected function printCommandRunExample(array $arguments, string $sprykName, OutputInterface $output): void
    {
        $consoleOutput = [
            'vendor/bin/diff-reviewer-run',
            $sprykName,
        ];

        foreach ($arguments as $argumentName => $argumentDefinition) {
            if (isset($argumentDefinition['value'])) {
                continue;
            }
            if (isset($argumentDefinition['default'])) {
                continue;
            }
            $consoleOutput[] = '--' . $argumentName;
            $consoleOutput[] = $argumentDefinition['example'] ?? $argumentName . 'Value';
        }

        $consoleOutput[] = '--no-interaction';

        $output->writeln('');
        $output->writeln('Use the following command to run this Spyk. You need to replace the placeholder values with your real value.');
        $output->writeln('');
        $output->writeln(sprintf('<fg=green>%s</>', implode(' ', $consoleOutput)));
        $output->writeln('');
    }

    /**
     * @throws \Exception
     */
    protected function formatDiffReviewers(array $sprykDefinitions): array
    {
        $formatted = [];
        foreach ($sprykDefinitions as $sprykName => $sprykDefinition) {
            if (!isset($sprykDefinition['description'])) {
                throw new Exception(sprintf('The DiffReviewer "%s" doesn\'t have a description.', $sprykName));
            }
            $formatted[$sprykName] = [
                $sprykName,
                $sprykDefinition['description'],
                $this->formatArguments($sprykDefinition),
            ];
        }
        sort($formatted);

        return $formatted;
    }

    protected function formatArguments(array $sprykDefinition): string
    {
        return implode(', ', array_keys($sprykDefinition[DiffReviewerConfig::SPRYK_DEFINITION_KEY_ARGUMENTS]));
    }
}
