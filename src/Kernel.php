<?php

/**
 * Copyright © 2016-present DiffReviewer Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace DiffReviewer;

use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symplify\AutowireArrayParameter\DependencyInjection\CompilerPass\AutowireArrayParameterCompilerPass;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    /**
     * Need to be defined manually as the PHAR doesn't contain a composer.json which is used by Symfony to detect the project root.
     *
     * @return string
     */
    public function getProjectDir(): string
    {
        return __DIR__ . '/..';
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     *
     * @return void
     */
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
    }

    /**
     * @codeCoverageIgnore
     *
     * Gets the container class.
     *
     * @throws \InvalidArgumentException If the generated classname is invalid
     *
     * @return string
     */
    protected function getContainerClass(): string
    {
        $class = static::class;
        $class = strpos($class, "@anonymous\0") !== false ? get_parent_class($class) . str_replace('.', '_', ContainerBuilder::hash($class)) : $class;
        $class = str_replace('\\', '_', $class) . ucfirst($this->environment) . ($this->debug ? 'Debug' : '') . 'Container';

        $pattern = '/_DiffReviewer_([a-zA-Z0-9]+)_/';
        preg_match($pattern, $class, $matches);
        if (isset($matches[1])) {
            $class = str_replace("_DiffReviewer_{$matches[1]}_", '', $class);
        }

        if (!preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $class)) {
            throw new InvalidArgumentException(sprintf('The environment "%s" contains invalid characters, it can only contain characters allowed in PHP class names.', $this->environment));
        }

        return $class;
    }
}
