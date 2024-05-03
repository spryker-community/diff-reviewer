<?php

/**
 * Copyright © 2016-present DiffReviewer Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace DiffReviewer\DiffReviewer\Console;

use DiffReviewer\DiffReviewer\Model\DiffReviewer\Checker\Validator\Rules\CheckerValidatorRuleInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CheckDiffReviewerDefinition extends AbstractDiffReviewerConsole
{
    /**
     * @var string
     */
    protected const COMMAND_NAME = 'diff-reviewer:check-definition';

    /**
     * @var string
     */
    protected const COMMAND_DESCRIPTION = 'Runs a DiffReviewer definition check process.';

    /**
     * @var string
     */
    public const ARGUMENT_SPRYK = 'diff-reviewer';

    /**
     * @var string
     */
    public const OPTION_FIX = 'fix';

    /**
     * @var string
     */
    public const OPTION_FIX_SHORT = 'f';

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this->setName(static::COMMAND_NAME)
            ->setDescription(static::COMMAND_DESCRIPTION)
            ->addArgument(
                static::ARGUMENT_SPRYK,
                InputOption::VALUE_OPTIONAL,
                'Name of a specific DiffReviewer for which the options should be dumped.',
            )
            ->addOption(
                static::OPTION_FIX,
                static::OPTION_FIX_SHORT,
                InputOption::VALUE_OPTIONAL,
                'DiffReviewer fix mode.',
                false,
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
        $isFix = $input->getOption(static::OPTION_FIX) === null;

        if ($isFix) {
            $this->getFacade()->fixDiffReviewerDefinitions();
        } else {
            $validationResult = $this->getFacade()->checkDiffReviewerDefinitions();
            if (
                isset($validationResult[CheckerValidatorRuleInterface::HAVE_ERRORS])
                || isset($validationResult[CheckerValidatorRuleInterface::HAVE_WARNINGS])
            ) {
                $this->printDiffReviewerDefinitionsErrorsAndWarnings($output, $validationResult);

                return isset($validationResult[CheckerValidatorRuleInterface::HAVE_ERRORS]) ? static::CODE_ERROR : static::CODE_WARNING;
            }
        }

        $this->printSuccessfulMessage($output);

        return static::CODE_SUCCESS;
    }

    protected function printDiffReviewerDefinitionsErrorsAndWarnings(OutputInterface $output, array $validationResult): void
    {
        [$errors, $warnings] = $this->prepareDiffReviewerDefinitionsErrorsAndWarnings($validationResult);

        foreach (array_keys($validationResult['definitions']) as $sprykName) {
            if (isset($errors[$sprykName])) {
                $this->printInfoMessage($output, sprintf('List of the %s DiffReviewer errors.', $sprykName));
                foreach ($errors[$sprykName] as $rule) {
                    foreach ($rule->getErrorMessages() as $error) {
                        $this->printErrorMessage($output, $error);
                    }
                }
            }

            if (isset($warnings[$sprykName])) {
                $this->printInfoMessage($output, sprintf('List of the %s DiffReviewer warnings.', $sprykName));
                foreach ($warnings[$sprykName] as $warning) {
                    $this->printWarningMessage($output, $warning);
                }
            }
        }

        if (!isset($warnings[CheckerValidatorRuleInterface::GENERAL_WARNINGS])) {
            return;
        }

        $this->printInfoMessage($output, 'List of the general DiffReviewer warnings:');

        foreach ($warnings[CheckerValidatorRuleInterface::GENERAL_WARNINGS] as $generalWarning) {
            $this->printWarningMessage($output, $generalWarning);
        }
    }

    protected function prepareDiffReviewerDefinitionsErrorsAndWarnings(array $validationResult): array
    {
        $errors = [];
        $warnings = [];

        foreach ($validationResult['definitions'] as $sprykName => $checkedDiffReviewerDefinition) {
            foreach ($checkedDiffReviewerDefinition[CheckerValidatorRuleInterface::ERRORS_KEY] as $ruleKey => $rule) {
                    $errors[$sprykName][$ruleKey] = $rule;
            }

            if (!isset($checkedDiffReviewerDefinition[CheckerValidatorRuleInterface::WARNINGS_RULE_KEY])) {
                continue;
            }

            foreach ($checkedDiffReviewerDefinition[CheckerValidatorRuleInterface::WARNINGS_RULE_KEY] as $warningMessage) {
                $warnings[$sprykName][] = $warningMessage;
            }
        }

        if (isset($validationResult[CheckerValidatorRuleInterface::GENERAL_WARNINGS])) {
            $warnings[CheckerValidatorRuleInterface::GENERAL_WARNINGS]
                = $validationResult[CheckerValidatorRuleInterface::GENERAL_WARNINGS];
        }

        return [$errors, $warnings];
    }

    protected function printErrorMessage(OutputInterface $output, string $message): void
    {
        $output->writeln('<error>' . $message . '</error>');
    }

    protected function printWarningMessage(OutputInterface $output, string $message): void
    {
        $output->writeln($message);
    }

    protected function printInfoMessage(OutputInterface $output, string $message): void
    {
        $output->writeln('<comment>' . $message . '</comment>');
    }

    protected function printSuccessfulMessage(OutputInterface $output): void
    {
        $output->writeln('<info>No validation errors found</info>');
    }
}
