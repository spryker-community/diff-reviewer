<?php

namespace DiffReviewer\DiffReviewer\Tagger;

interface FileTaggerInterface
{
    public function tag($file, array $tags): array;
}
