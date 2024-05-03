<?php

/**
 * Copyright © 2016-present DiffReviewer Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace DiffReviewer\DiffReviewer\Console;

use DiffReviewer\DiffReviewer\DiffReviewerFacadeInterface;
use Symfony\Component\Console\Command\Command;

abstract class AbstractDiffReviewerConsole extends Command
{
    /**
     * @var int
     */
    protected const CODE_SUCCESS = 0;

    /**
     * @var int
     */
    protected const CODE_ERROR = 1;

    /**
     * @var int
     */
    protected const CODE_WARNING = 2;

    /**
     * @param \DiffReviewer\DiffReviewer\DiffReviewerFacadeInterface $facade
     * @param string|null $name
     */
    public function __construct(protected DiffReviewerFacadeInterface $facade, ?string $name = null)
    {
        parent::__construct($name);
    }

    /**
     * @return \DiffReviewer\DiffReviewer\DiffReviewerFacadeInterface
     */
    public function getFacade(): DiffReviewerFacadeInterface
    {
        return $this->facade;
    }
}
