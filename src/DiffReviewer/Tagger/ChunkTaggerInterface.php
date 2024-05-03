<?php

namespace DiffReviewer\DiffReviewer\Tagger;

interface ChunkTaggerInterface
{
    public function tag($chunk, array $tags): array;
}
