<?php

namespace DiffReviewer\DiffReviewer\Strategy;

use \IteratorAggregate;

class StrategyRunner
{

    /**
     * @param IteratorAggregate<\DiffReviewer\DiffReviewer\Strategy\StrategyInterface> $strategies
     */
    public function __construct()
    {
        $this->strategies = [
            new \DiffReviewer\DiffReviewer\Strategy\Changelog\TransferChangelogStrategy(),
        ];
    }

    public function getChangelog($diff, $changelogData): array
    {
        foreach ($this->strategies as $strategy) {
            if (!$strategy->isApplicable($diff)) {
                continue;
            }
            $changelogData = $strategy->run($diff, $changelogData);
        }

        return $changelogData;
    }
}
