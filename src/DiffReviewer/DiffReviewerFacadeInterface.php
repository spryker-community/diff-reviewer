<?php

/**
 * Copyright © 2016-present DiffReviewer Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace DiffReviewer\DiffReviewer;

interface DiffReviewerFacadeInterface
{
    public function generateChangelog(): string;

    public function review(): void;
}
