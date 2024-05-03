<?php

namespace DiffReviewer\DiffReviewer\Tagger\FileTagger;

use SebastianBergmann\Diff\Diff;
use DiffReviewer\DiffReviewer\Tagger\FileTaggerInterface;

class FileTypeTagger implements FileTaggerInterface
{
    public function tag(Diff $file, array $tags): array
    {
        $fileName = $file->from();

        if ($fileName === '/dev/null') {
            $fileName = $file->to();
        }

        $fileNameParts = explode('.', $fileName);

        $tags[] = end($fileNameParts);

        return $tags;
    }
}
