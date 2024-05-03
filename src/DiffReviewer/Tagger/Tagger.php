<?php

namespace DiffReviewer\DiffReviewer\Tagger;

use IteratorAggregate;

class Tagger
{
    /**
     * @param IteratorAggregate<\DiffReviewer\DiffReviewer\Tagger\FileTaggerInterface> $fileTaggers
     * @param IteratorAggregate<\DiffReviewer\DiffReviewer\Tagger\ChunkTaggerInterface> $chunkTaggers
     */
    public function __construct(protected IteratorAggregate $fileTaggers, protected IteratorAggregate $chunkTaggers)
    {
    }

    public function tagDiff(array $diff): array
    {
        foreach ($diff as $fileDiff) {
            $fileDiff['tags'] = $this->getFileTags($fileDiff);

            foreach ($fileDiff as $chunk) {
                $chunk['tags'] = $this->getChunkTags($chunk);
            }
        }

        return $diff;
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
