<?php

/**
 * Copyright © 2016-present DiffReviewer Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace DiffReviewerTest\DiffReviewer\Console;

use Codeception\Test\Unit;
use DiffReviewer\DiffReviewer\Console\DiffReviewerDumpConsole;

/**
 * Auto-generated group annotations
 *
 * @group DiffReviewerTest
 * @group DiffReviewer
 * @group Console
 * @group DiffReviewerDumpTest
 * Add your own group annotations below this line
 */
class DiffReviewerCreateChangelogTest extends Unit
{
    /**
     * @var \DiffReviewerTest\DiffReviewerConsoleTester
     */
    protected $tester;

    /**
     * @return void
     */
    public function testDumpsAllDiffReviewers(): void
    {
        $command = $this->createDiffReviewerDumpConsole();
        $tester = $this->tester->getConsoleTester($command);

        $arguments = [
            'command' => $command->getName(),
        ];

        $tester->execute($arguments);

        $output = $tester->getDisplay();

        $this->assertRegExp('/List of DiffReviewer definitions/', $output);
    }

    /**
     * @return void
     */
    public function testDumpsSpecificDiffReviewer(): void
    {
        $command = $this->createDiffReviewerDumpConsole();
        $tester = $this->tester->getConsoleTester($command);

        $arguments = [
            'command' => $command->getName(),
            DiffReviewerDumpConsole::ARGUMENT_SPRYK => 'AddModule',
        ];

        $tester->execute($arguments);

        $output = $tester->getDisplay();
        $this->assertStringContainsString('Description of the AddModule DiffReviewer', $output);
        $this->assertStringContainsString('The following arguments are required and you need to pass them', $output);
        $this->assertStringContainsString('The following arguments are optional and you can pass them when needed', $output);
        $this->assertStringContainsString('This DiffReviewer does not have any preDiffReviewer to be executed', $output);
        $this->assertStringContainsString('Post DiffReviewers which are executed after the DiffReviewer was running', $output);
        $this->assertStringContainsString('Use the following command to run this Spyk. You need to replace the placeholder values with your real value', $output);
        $this->assertStringContainsString('vendor/bin/diff-reviewer-run AddModule --module moduleValue --organization organizationValue', $output);
    }

    /**
     * @return void
     */
    public function testDumpsSpecificDiffReviewerWithoutRequiredArguments(): void
    {
        $command = $this->createDiffReviewerDumpConsole();
        $tester = $this->tester->getConsoleTester($command);

        $arguments = [
            'command' => $command->getName(),
            DiffReviewerDumpConsole::ARGUMENT_SPRYK => 'ExampleWithoutRequiredArguments',
        ];

        $tester->execute($arguments);

        $this->assertStringContainsString('This DiffReviewer does not have any required arguments to be passed', $tester->getDisplay());
    }

    /**
     * @return void
     */
    public function testDumpsSpecificDiffReviewerWithoutOptionalArgumentsPrintsTableAsModeIsAlwaysAddedTOEveryDiffReviewer(): void
    {
        $command = $this->createDiffReviewerDumpConsole();
        $tester = $this->tester->getConsoleTester($command);

        $arguments = [
            'command' => $command->getName(),
            DiffReviewerDumpConsole::ARGUMENT_SPRYK => 'ExampleWithoutOptionalArguments',
        ];

        $tester->execute($arguments);

        // Check if we can see in the output the default mode
        $this->assertStringContainsString('project', $tester->getDisplay());
    }

    /**
     * @return void
     */
    public function testDumpsSpecificDiffReviewerPreDiffReviewers(): void
    {
        $command = $this->createDiffReviewerDumpConsole();
        $tester = $this->tester->getConsoleTester($command);

        $arguments = [
            'command' => $command->getName(),
            DiffReviewerDumpConsole::ARGUMENT_SPRYK => 'ExampleWithPreDiffReviewers',
        ];

        $tester->execute($arguments);

        $this->assertStringContainsString('AddModule', $tester->getDisplay());
    }

    /**
     * @return void
     */
    public function testDumpsSpecificDiffReviewerPostDiffReviewers(): void
    {
        $command = $this->createDiffReviewerDumpConsole();
        $tester = $this->tester->getConsoleTester($command);

        $arguments = [
            'command' => $command->getName(),
            DiffReviewerDumpConsole::ARGUMENT_SPRYK => 'ExampleWithPostDiffReviewers',
        ];

        $tester->execute($arguments);

        $this->assertStringContainsString('AddReadme', $tester->getDisplay());
        $this->assertStringContainsString('AddReadme', $tester->getDisplay());
    }

    /**
     * @return \DiffReviewer\DiffReviewer\Console\DiffReviewerDumpConsole
     */
    protected function createDiffReviewerDumpConsole(): DiffReviewerDumpConsole
    {
        return $this->tester->getClass(DiffReviewerDumpConsole::class);
    }
}
