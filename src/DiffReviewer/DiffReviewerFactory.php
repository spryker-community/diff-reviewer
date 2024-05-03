<?php

/**
 * Copyright © 2016-present DiffReviewer Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace DiffReviewer\DiffReviewer;

class DiffReviewerFactory
{
    public function __construct(
        public DiffReviewerConfig $config,
    ) {
    }
}
