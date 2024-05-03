<?php

namespace DiffReviewer\DiffReviewer\Differ;

use DiffReviewer\DiffReviewer\Git\GitClient;
use SebastianBergmann\Diff\Parser;

class Differ
{
    public function __construct(protected GitClient $gitClient)
    {
    }

    /**
     * Hardcoded for now, but input from facade should be used
     */
    protected const PR_LINK = 'https://github.com/spryker/spryker/pull/10768';

    public function getDiff(): array
    {
        return $this->getDiffFromPR();
    }

    protected function getDiffFromPR(): array
    {
        $diff = $this->gitClient->getPrDiff();
        $parser = new Parser();

        return $parser->parse($diff);
    }
}
