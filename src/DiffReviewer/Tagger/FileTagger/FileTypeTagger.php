<?php

namespace DiffReviewer\DiffReviewer\Tagger\FileTagger;

class FileTypeTagger implements \DiffReviewer\DiffReviewer\Tagger\FileTaggerInterface
{
    public function tag($file, array $tags): array
    {
        if (strpos($file['name'], '.php') !== false) {
            $tags[] = 'php';
        }

        return $tags;
    }
}
