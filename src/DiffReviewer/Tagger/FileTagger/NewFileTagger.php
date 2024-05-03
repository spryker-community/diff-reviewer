<?php

namespace DiffReviewer\DiffReviewer\Tagger\FileTagger;

use SebastianBergmann\Diff\Diff;

class NewFileTagger implements \DiffReviewer\DiffReviewer\Tagger\FileTaggerInterface
{
    public function tag(Diff $file, array $tags): array
    {
        if ($file->from() === '/dev/null') {
            $tags[] = 'new';
        }

        return $tags;
    }
}
