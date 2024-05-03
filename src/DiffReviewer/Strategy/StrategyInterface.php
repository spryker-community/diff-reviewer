<?php

namespace DiffReviewer\DiffReviewer\Strategy;

interface StrategyInterface
{
    public function isApplicable(array $diff): bool;
    public function run($diff, array $changelogData): array;
}
