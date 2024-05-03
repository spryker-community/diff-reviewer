<?php

namespace DiffReviewer\DiffReviewer\Tagger\ChunkTagger;

use SebastianBergmann\Diff\Chunk;

class ChunkTagger implements \DiffReviewer\DiffReviewer\Tagger\ChunkTaggerInterface
{
    public function tag(Chunk $file, array $tags): array
    {
        $tags[] = 'chunk tag';

        return $tags;
    }
}
