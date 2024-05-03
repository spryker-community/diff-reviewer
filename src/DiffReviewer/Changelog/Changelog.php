<?php

namespace DiffReviewer\DiffReviewer\Changelog;

use DiffReviewer\DiffReviewer\Differ\Differ;
use DiffReviewer\DiffReviewer\Strategy\StrategyRunner;
use DiffReviewer\DiffReviewer\Tagger\Tagger;

class Changelog
{
    public function __construct(
        protected Differ $differ,
        protected Tagger $tagger,
        protected StrategyRunner $strategyRunner
    ) {}


    public function generateChangelog(): string
    {
        // Get the diff
        $diff = $this->differ->getDiff();

        $taggedDiff = $this->tagger->tagDiff($diff);

        $changelogData = [];

        foreach ($taggedDiff as $item) {
            $changelogData = $this->strategyRunner->getChangelog($item, $changelogData);
        }

        return $this->generateReleaseTemplate($changelogData);
    }

    protected function generateReleaseTemplate(array $changeLogData): string
    {
        $generatedChangelog = <<<EOT
- Developer: @yaroslav-spryker

- Ticket: https://spryker.atlassian.net/browse/FRW-7102

- Release Group: https://release.spryker.com/release-groups/view/5274

- PR Overview: https://release.spryker.com/release-groups/view/5274

- merge: squash

#### Release Table

   Module                | Release Type         | Constraint Updates         |
   :--------------------- | :------------------------ | :--------------------- |
EOT . PHP_EOL;

        foreach ($changeLogData as $moduleName => $moduleData) {
            $releaseType = $moduleData['releaseType'];

            $generatedChangelog .= <<<EOT
$moduleName  | $releaseType                 |        |
EOT . PHP_EOL;
        }
        $generatedChangelog .= <<<EOT

-----------------------------------------

EOT . PHP_EOL;

        foreach ($changeLogData as $moduleName => $moduleData) {
            $generatedChangelog .= <<<EOT
#### Module $moduleName

##### Change log

EOT . PHP_EOL;
            foreach ($moduleData as $type => $changes) {
                //Ugly hack to get the type
                if ($type === 'releaseType') {
                    continue;
                }
                $generatedChangelog .= <<<EOT
$type
EOT . PHP_EOL . PHP_EOL;
                foreach ($changes as $change) {
                    $generatedChangelog .= <<<EOT
- $change
EOT . PHP_EOL;
                }
            }
        }
        return $generatedChangelog;
    }
}
