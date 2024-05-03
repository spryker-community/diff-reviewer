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

        var_dump($changelogData);die;

        // ???? Iterate over and tag

        $generatedChangelog = <<<EOT
- Developer: @yaroslav-spryker

- Ticket: https://spryker.atlassian.net/browse/FRW-7102

- Release Group: https://release.spryker.com/release-groups/view/5274

- PR Overview: https://release.spryker.com/release-groups/view/5274

- merge: squash

#### Release Table

   Module                | Release Type         | Constraint Updates         |
   :--------------------- | :------------------------ | :--------------------- |
   DynamicEntity  | minor                 |        |
   DynamicEntityBackendApi  | minor                 |   DynamicEntity     |

-----------------------------------------

#### Module DynamicEntity

##### Change log

Fixes

- Adjusted `DynamicEntityFacade::updateDynamicEntityCollection()` so now it does not require identifier when `DynamicEntityCollectionRequestTransfer::\$isCreatable` is set to `true`.

Improvements

- Introduced `DynamicEntityCollectionRequestTransfer.resetNotProvidedFieldValues ` transfer field.

-----------------------------------------

#### Module DynamicEntityBackendApi

##### Change log

Fixes

- Adjusted `DynamicEntityBackendApiController::putAction()`, so now it resets entity field values in case they are present in the configuration, but not provided in the request.

Improvements

- Introduced `DynamicEntityCollectionRequestTransfer.resetNotProvidedFieldValues` transfer field.
- Adjusted `DynamicEntityBackendApiController::putAction()` to add support for child relation saving.

Adjustments

- Increased `DynamicEntity` module version dependency.

EOT;
        return $generatedChangelog;
    }
}
