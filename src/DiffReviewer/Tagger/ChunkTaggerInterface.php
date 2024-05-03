<?php

namespace DiffReviewer\DiffReviewer\Tagger;

use SebastianBergmann\Diff\Chunk;

interface ChunkTaggerInterface
{
    public function tag(Chunk $chunk, array $tags): array;
}
