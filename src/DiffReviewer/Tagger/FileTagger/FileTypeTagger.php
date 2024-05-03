<?php

namespace DiffReviewer\DiffReviewer\Tagger\FileTagger;

use SebastianBergmann\Diff\Diff;

class FileTypeTagger implements \DiffReviewer\DiffReviewer\Tagger\FileTaggerInterface
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
