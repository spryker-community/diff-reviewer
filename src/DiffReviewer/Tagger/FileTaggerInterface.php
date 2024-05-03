<?php

namespace DiffReviewer\DiffReviewer\Tagger;

use SebastianBergmann\Diff\Diff;

interface FileTaggerInterface
{
    public function tag(Diff $file, array $tags): array;
}
