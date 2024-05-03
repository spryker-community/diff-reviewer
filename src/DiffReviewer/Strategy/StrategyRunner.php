<?php

namespace DiffReviewer\DiffReviewer\Strategy;

use \IteratorAggregate;

class StrategyRunner
{

    /**
     * @param IteratorAggregate<\DiffReviewer\DiffReviewer\Strategy\StrategyInterface> $strategies
     */
    public function __construct(protected IteratorAggregate $strategies)
    {
    }

    public function getChangelog($diff): array
    {
        foreach ($this->strategies as $strategy) {
            $diff = $strategy->run($diff, $changelogData);
        }

        return $diff;
    }
}
