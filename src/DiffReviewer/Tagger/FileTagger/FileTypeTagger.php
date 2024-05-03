<?php

namespace DiffReviewer\DiffReviewer\Tagger\FileTagger;

use SebastianBergmann\Diff\Diff;

class FileTypeTagger implements \DiffReviewer\DiffReviewer\Tagger\FileTaggerInterface
{
    public function tag(Diff $file, array $tags): array
    {
        $from = $file->from();

        if (strpos($from, '.php') !== false) {
            $tags[] = 'php';
        }

        if (strpos($from, '.neon') !== false) {
            $tags[] = 'neon';
        }

        return $tags;
    }
}
