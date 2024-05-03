<?php

/**
 * Copyright © 2016-present DiffReviewer Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use PhpParser\Lexer;
use PhpParser\Parser;
use PhpParser\PrettyPrinter\Standard;
use DiffReviewer\DiffReviewer\Console\DiffReviewerRunConsole;
use DiffReviewer\DiffReviewer\Model\DiffReviewer\ArgumentList\Generator\ArgumentListGenerator;
use DiffReviewer\DiffReviewer\Model\DiffReviewer\ArgumentList\Reader\ArgumentListReader;
use DiffReviewer\DiffReviewer\Model\DiffReviewer\Builder\Resolver\FileResolver;
use DiffReviewer\DiffReviewer\Model\DiffReviewer\Builder\Resolver\Parser\ClassParser;
use DiffReviewer\DiffReviewer\Model\DiffReviewer\Builder\Resolver\Parser\FileParser;
use DiffReviewer\DiffReviewer\Model\DiffReviewer\Builder\Resolver\Parser\JsonParser;
use DiffReviewer\DiffReviewer\Model\DiffReviewer\Builder\Resolver\Parser\ParserInterface;
use DiffReviewer\DiffReviewer\Model\DiffReviewer\Builder\Resolver\Parser\XmlParser;
use DiffReviewer\DiffReviewer\Model\DiffReviewer\Builder\Resolver\Parser\YmlParser;
use DiffReviewer\DiffReviewer\Model\DiffReviewer\Builder\Structure\StructureDiffReviewer;
use DiffReviewer\DiffReviewer\Model\DiffReviewer\Command\ComposerDumpAutoloadDiffReviewerCommand;
use DiffReviewer\DiffReviewer\Model\DiffReviewer\Command\ComposerReplaceGenerateDiffReviewerCommand;
use DiffReviewer\DiffReviewer\Model\DiffReviewer\Configuration\Loader\DiffReviewerConfigurationLoader;
use DiffReviewer\DiffReviewer\Model\DiffReviewer\Dumper\DiffReviewerDefinitionDumper;
use DiffReviewer\DiffReviewer\Model\DiffReviewer\Executor\DiffReviewerExecutor;
use DiffReviewer\DiffReviewer\DiffReviewerConfig;
use DiffReviewer\DiffReviewer\DiffReviewerFactory;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

return function (ContainerConfigurator $configurator): void {
    $services = $configurator->services()
        ->defaults()
            ->autowire()
            ->autoconfigure();

    $services->load('DiffReviewer\\', '../src/')
        ->exclude('../src/{DependencyInjection,Tests,Kernel.php}');

    $services->load('Laminas\\Filter\\', __DIR__ . '/../vendor/laminas/laminas-filter/src/')
        ->exclude([
            // these depend on Laminas' service managers
            __DIR__ . '/../vendor/laminas/laminas-filter/src/{*Factory,FilterPluginManager}.php',
            __DIR__ . '/../vendor/laminas/laminas-filter/src/**/*Factory.php',
        ]);

    // To prevent Symfony from injecting a filesystem cache we pass `null` and an empty array to let the `ExpressionLanguage`
    // use the `ArrayAdapter`. Without overriding this, the `ExpressionLanguage` tries to write into the filesystem during
    // runtime which is not possible with PHAR archives.
    $services->set(ExpressionLanguage::class)
        ->args([null, []]);

    // Make DiffReviewerConfig public for testing
    // This should go to services_testing
//    $services->get(DiffReviewerConfig::class)->public();

    // Make services lazy
    // https://symfony.com/doc/current/service_container/lazy_services.html
//    $services->set(DiffReviewerExecutor::class)->lazy();


    // https://symfony.com/doc/current/service_container/autowiring.html#dealing-with-multiple-implementations-of-the-same-type
    // TODO either refactor to use delegating service or automate with a CompilerPass
    $services->alias(ParserInterface::class . ' $classParser', ClassParser::class);
    $services->alias(ParserInterface::class . ' $fileParser', FileParser::class);
    $services->alias(ParserInterface::class . ' $jsonParser', JsonParser::class);
    $services->alias(ParserInterface::class . ' $ymlParser', YmlParser::class);
    $services->alias(ParserInterface::class . ' $xmlParser', XmlParser::class);

    // https://symfony.com/doc/current/service_container/factories.html
    $services->set(Standard::class)
        ->factory([service(DiffReviewerFactory::class), 'createClassPrinter'])
        ->args([service(DiffReviewerConfig::class)]);

    $services->set(Parser::class)
        ->factory([service(DiffReviewerFactory::class), 'createParser'])
        ->args([service(DiffReviewerConfig::class)]);

    $services->set(Lexer::class)
        ->factory([service(DiffReviewerFactory::class), 'createLexer'])
        ->args([service(DiffReviewerConfig::class)]);

    if ($configurator->env() === 'test') {
//        $services->get(StructureDiffReviewer::class)->public();
    }

    $services->set(\DiffReviewer\DiffReviewer\Tagger\FileTaggerInterface::class)
        ->tag(\DiffReviewer\DiffReviewer\Tagger\FileTaggerInterface::class);

    $services->set(\DiffReviewer\DiffReviewer\Tagger\ChunkTaggerInterface::class)
        ->tag(\DiffReviewer\DiffReviewer\Tagger\ChunkTaggerInterface::class);

    $services->set(\DiffReviewer\DiffReviewer\Tagger\Tagger::class)
        ->args([
            tagged_iterator(\DiffReviewer\DiffReviewer\Tagger\FileTaggerInterface::class),
            tagged_iterator(\DiffReviewer\DiffReviewer\Tagger\ChunkTaggerInterface::class),
        ]);
};
