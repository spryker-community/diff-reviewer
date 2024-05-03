<?php

namespace DiffReviewer\DiffReviewer\Tagger\FileTagger;

use SebastianBergmann\Diff\Diff;
use DiffReviewer\DiffReviewer\Tagger\FileTaggerInterface;

class NewFileTagger implements FileTaggerInterface
{
    public function tag(Diff $file, array $tags): array
    {
        if ($file->from() === '/dev/null') {
            $tags[] = 'new';
        }

        return $tags;
    }
}
