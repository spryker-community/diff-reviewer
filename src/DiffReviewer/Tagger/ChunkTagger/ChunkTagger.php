<?php

namespace DiffReviewer\DiffReviewer\Tagger\ChunkTagger;

class ChunkTagger implements \DiffReviewer\DiffReviewer\Tagger\ChunkTaggerInterface
{
    public function tag($file, array $tags): array
    {
        $tags[] = 'chunk tag';

        return $tags;
    }
}
