<?php

namespace DiffReviewer\DiffReviewer\Strategy;

interface StrategyInterface
{
    public function run($diff, array $changelogData): array;
}
