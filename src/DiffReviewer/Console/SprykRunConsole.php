<?php

/**
 * Copyright © 2016-present DiffReviewer Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace DiffReviewer\DiffReviewer\Console;

use RuntimeException;
use DiffReviewer\DiffReviewer\Model\DiffReviewer\Definition\Argument\Resolver\OptionsContainer;
use DiffReviewer\DiffReviewer\Model\DiffReviewer\Executor\Configuration\DiffReviewerExecutorConfigurationInterface;
use DiffReviewer\DiffReviewer\DiffReviewerConfig;
use DiffReviewer\DiffReviewer\DiffReviewerFacadeInterface;
use DiffReviewer\DiffReviewer\Style\DiffReviewerStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DiffReviewerRunConsole extends AbstractDiffReviewerConsole
{
    /**
     * @var string
     */
    protected const COMMAND_NAME = 'diff-reviewer:run';

    /**
     * @var string
     */
    protected const COMMAND_DESCRIPTION = 'Runs a DiffReviewer build process.';

    /**
     * @var string
     */
    public const ARGUMENT_SPRYK = 'diff-reviewer';

    /**
     * @var string
     */
    public const ARGUMENT_TARGET_MODULE = 'targetModule';

    /**
     * @var string
     */
    public const ARGUMENT_DEPENDENT_MODULE = 'dependentModule';

    /**
     * @var string
     */
    public const OPTION_INCLUDE_OPTIONALS = 'include-optional';

    /**
     * @var string
     */
    public const OPTION_INCLUDE_OPTIONALS_SHORT = 'i';

    /**
     * @var array|null
     */
    protected static ?array $argumentsList = null;

    /**
     * @param \DiffReviewer\DiffReviewer\Model\DiffReviewer\Executor\Configuration\DiffReviewerExecutorConfigurationInterface $executorConfiguration
     * @param \DiffReviewer\DiffReviewer\DiffReviewerFacadeInterface $facade
     * @param string|null $name
     */
    public function __construct(protected DiffReviewerExecutorConfigurationInterface $executorConfiguration, DiffReviewerFacadeInterface $facade, ?string $name = null)
    {
        parent::__construct($facade, $name);
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this->setName(static::COMMAND_NAME)
            ->setDescription(static::COMMAND_DESCRIPTION)
            ->setHelp($this->getHelpText())
            ->addArgument(static::ARGUMENT_SPRYK, InputArgument::REQUIRED, 'Name of the DiffReviewer which should be build.')
            ->addArgument(static::ARGUMENT_TARGET_MODULE, InputArgument::OPTIONAL, 'Name of the target module in format "[Organization.]ModuleName[.LayerName]".')
            ->addArgument(static::ARGUMENT_DEPENDENT_MODULE, InputArgument::OPTIONAL, 'Name of the dependent module in format "[Organization.]ModuleName[.LayerName]".')
            ->addOption(static::OPTION_INCLUDE_OPTIONALS, static::OPTION_INCLUDE_OPTIONALS_SHORT, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Name(s) of the DiffReviewers which are marked as optional but should be build.')
            ->addOption('dry-run', 'd', InputOption::VALUE_NONE, 'Only print a diff, do not change files')
            ->addOption('debug', 'dd', InputOption::VALUE_NONE, 'Print standard debug information. This will only print DiffReviewer Names in their executed order.')
            ->addOption('debug-verbose', 'ddd', InputOption::VALUE_NONE, 'Print verbose debug information. This will print DiffReviewer Names in their executed order and the resolved arguments.')
            ->addOption('debug-very-verbose', 'dddd', InputOption::VALUE_NONE, 'Print very verbose debug information. This will print DiffReviewer Names in their executed order, the resolved arguments, and why the argument was resolved to the current value.');

        foreach ($this->getDiffReviewerArguments() as $argumentDefinition) {
            $this->addOption(
                $argumentDefinition['name'],
                null,
                $argumentDefinition[DiffReviewerConfig::NAME_ARGUMENT_MODE],
                $argumentDefinition['description'],
            );
        }
    }

    protected function getDiffReviewerArguments(): array
    {
        if (static::$argumentsList === null) {
            static::$argumentsList = $this->getFacade()->getArgumentList();
        }

        return array_filter(static::$argumentsList, function (array $argumentDefinition) {
            return !str_contains($argumentDefinition['name'], '.');
        });
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        OptionsContainer::setOptions($input->getOptions());

        $sprykExecutorConfiguration = $this->executorConfiguration->prepare(
            $this->getDiffReviewerName($input),
            (array)OptionsContainer::getOption(static::OPTION_INCLUDE_OPTIONALS),
            $this->getTargetModuleName($input),
            $this->getDependentModuleName($input),
        );

        $this->getFacade()->executeDiffReviewer(
            $sprykExecutorConfiguration,
            new DiffReviewerStyle($input, $output),
        );

        return static::CODE_SUCCESS;
    }

    /**
     * @throws \RuntimeException
     */
    protected function getDiffReviewerName(InputInterface $input): string
    {
        $name = current((array)$input->getArgument(static::ARGUMENT_SPRYK));
        if ($name === false) {
            throw new RuntimeException('Cannot retrieve DiffReviewer name');
        }

        return $name;
    }

    protected function getTargetModuleName(InputInterface $input): string
    {
        return current((array)$input->getArgument(static::ARGUMENT_TARGET_MODULE)) ?: '';
    }

    protected function getDependentModuleName(InputInterface $input): string
    {
        return current((array)$input->getArgument(static::ARGUMENT_DEPENDENT_MODULE)) ?: '';
    }

    protected function getHelpText(): string
    {
        return 'Use `console diff-reviewer:dump <info>{SPRYK NAME}</info>` to get the options of a specific DiffReviewer.';
    }
}
