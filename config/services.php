<?php

/**
 * Copyright © 2016-present DiffReviewer Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use DiffReviewer\DiffReviewer\Tagger\ChunkTagger\ChunkTagger;
use DiffReviewer\DiffReviewer\Tagger\FileTagger\FileTypeTagger;
use DiffReviewer\DiffReviewer\Tagger\FileTagger\NewFileTagger;
use DiffReviewer\DiffReviewer\Tagger\FileTagger\PluginTagger;
use DiffReviewer\DiffReviewer\Tagger\FileTagger\SchemaTagger;
use DiffReviewer\DiffReviewer\Tagger\FileTaggerInterface;
use DiffReviewer\DiffReviewer\Tagger\ChunkTaggerInterface;
use DiffReviewer\DiffReviewer\Tagger\Tagger;
use Http\Client\Common\Plugin;

return function (ContainerConfigurator $configurator): void {
    $services = $configurator->services()
        ->defaults()
            ->autowire()
            ->autoconfigure();

    $services->load('DiffReviewer\\', '../src/')
        ->exclude('../src/{DependencyInjection,Tests,Kernel.php}');

    $services->set(FileTypeTagger::class)
        ->tag('app.file_tagger');

    $services->set(NewFileTagger::class)
        ->tag('app.file_tagger');

    $services->set(PluginTagger::class)
        ->tag('app.file_tagger');

    $services->set(SchemaTagger::class)
        ->tag('app.file_tagger');

    $services->set(ChunkTagger::class)
        ->tag('app.chunk_tagger');

    $services->instanceof(FileTaggerInterface::class)
        ->tag('app.file_tagger');

    $services->instanceof(ChunkTaggerInterface::class)
        ->tag('app.chunk_tagger');

    $services->set(Tagger::class)
        ->args([
            tagged_iterator('app.file_tagger'),
            tagged_iterator('app.chunk_tagger'),
        ]);
};
