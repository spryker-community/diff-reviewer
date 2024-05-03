<?php

/**
 * Copyright © 2016-present DiffReviewer Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace DiffReviewer\DiffReviewer;

use DiffReviewer\DiffReviewer\Changelog\Changelog;
use DiffReviewer\DiffReviewer\Model\DiffReviewer\Executor\Configuration\DiffReviewerExecutorConfigurationInterface;
use DiffReviewer\DiffReviewer\Style\DiffReviewerStyleInterface;

class DiffReviewerFacade implements DiffReviewerFacadeInterface
{
    public function __construct(
        protected DiffReviewerFactory $factory,
        protected Changelog $changelog
    ) {
    }

    public function generateChangelog(): string {
        return $this->changelog->generateChangelog();
    }

    public function review(): void
    {
        // TODO: Implement review() method.
    }
}
