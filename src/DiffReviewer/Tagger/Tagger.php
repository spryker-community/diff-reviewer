<?php

namespace DiffReviewer\DiffReviewer\Tagger;

use DiffReviewer\DiffReviewer\Tagger\ChunkTagger\ChunkTagger;
use DiffReviewer\DiffReviewer\Tagger\FileTagger\FileTypeTagger;
use DiffReviewer\DiffReviewer\Tagger\FileTagger\NewFileTagger;
use DiffReviewer\DiffReviewer\Tagger\FileTagger\PluginTagger;
use DiffReviewer\DiffReviewer\Tagger\FileTagger\SchemaTagger;
use IteratorAggregate;
use mysql_xdevapi\Schema;

class Tagger
{
    /**
     * @param IteratorAggregate<\DiffReviewer\DiffReviewer\Tagger\FileTaggerInterface> $fileTaggers
     * @param IteratorAggregate<\DiffReviewer\DiffReviewer\Tagger\ChunkTaggerInterface> $chunkTaggers
     */
    public function __construct(protected IteratorAggregate $fileTaggers, protected IteratorAggregate $chunkTaggers)
    {
    }

    public function tagDiff(array $diffCollection): array
    {
        $taggedDiff = [];

        foreach ($diffCollection as $diff) {
            $newDiff = [
                'diff' => $diff,
                'tags' => $this->getFileTags($diff),
            ];

            $taggedDiff[] = $newDiff;
        }

        return $taggedDiff;
    }

    protected function getFileTags($file): array
    {
        $tags = [];

        foreach ($this->fileTaggers as $fileTagger) {
            $tags = $fileTagger->tag($file, $tags);
        }

        return $tags;
    }

    protected function getChunkTags($chunk): array
    {
        $tags = [];

        foreach ($this->chunkTaggers as $chunkTagger) {
            $tags = $chunkTagger->tag($chunk, $tags);
        }

        return $tags;
    }
}
