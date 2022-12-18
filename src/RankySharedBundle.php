<?php
declare(strict_types=1);

namespace Ranky\SharedBundle;

use Ranky\SharedBundle\Infrastructure\DependencyInjection\ShareBundleCompilerPass;
use Ranky\SharedBundle\Infrastructure\DependencyInjection\SharedBundleExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;


class RankySharedBundle extends Bundle
{

    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new ShareBundleCompilerPass());
    }

    public function getContainerExtension(): ?ExtensionInterface
    {
        if (null === $this->extension) {
            $this->extension = new SharedBundleExtension();
        }

        return $this->extension;
    }

    public function getPath(): string
    {
        return \dirname(__DIR__);
    }


}
