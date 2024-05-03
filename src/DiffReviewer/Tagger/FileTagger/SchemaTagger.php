<?php

namespace DiffReviewer\DiffReviewer\Tagger\FileTagger;

use SebastianBergmann\Diff\Diff;

class SchemaTagger implements \DiffReviewer\DiffReviewer\Tagger\FileTaggerInterface
{
    public function tag(Diff $file, array $tags): array
    {
        $from = $file->from();

        if (str_ends_with($from, '.transfer.xml')) {
            $tags[] = 'transfer-schema';
        }

        if (str_ends_with($from, '.databuilder.xml')) {
            $tags[] = 'databuilder-schema';
        }

        if (str_ends_with($from, '.schema.xml')) {
            $tags[] = 'propel-schema';
        }

        return $tags;
    }
}
