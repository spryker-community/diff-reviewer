<?php

namespace DiffReviewer\DiffReviewer\Changelog;

use DiffReviewer\DiffReviewer\Differ\Differ;

class Changelog
{
    public function __construct(protected Differ $differ)
    {
    }


    public function generateChangelog(): string
    {
        // Get the diff
        $diff = $this->differ->getDiff();

        // ???? Iterate over and remove not needed files

            // Based on file type go into some Strategies like Xml, JsonStrategy, etc
            // Array of strategies
            // StrategyInterface


        // ???? Iterate over and tag





        return 'Changelog generated';
    }
}
