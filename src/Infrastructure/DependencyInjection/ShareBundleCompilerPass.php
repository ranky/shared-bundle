<?php

declare(strict_types=1);

namespace Ranky\SharedBundle\Infrastructure\DependencyInjection;


use Ranky\SharedBundle\Filter\Visitor\Extension\FilterExtensionVisitor;
use Ranky\SharedBundle\Filter\Visitor\Extension\FilterExtensionVisitorFacade;
use Ranky\SharedBundle\Filter\Visitor\VisitorCollection;
use Ranky\SharedBundle\Presentation\Exception\ApiProblemLogErrorListener;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;


class ShareBundleCompilerPass implements CompilerPassInterface
{

    public function process(ContainerBuilder $container): void
    {
        $definition = $container->getDefinition('exception_listener');
        $definition->setClass(ApiProblemLogErrorListener::class);

        if (!$container->has(VisitorCollection::class)) {
            return;
        }

        $visitorCollection       = $container->getDefinition(VisitorCollection::class);
        $filterExtensionVisitors = $container->findTaggedServiceIds(FilterExtensionVisitor::TAG_NAME);
        foreach ($filterExtensionVisitors as $id => $tags) {
            $facadeExtensionVisitor = new Definition(FilterExtensionVisitorFacade::class, [new Reference($id)]);
            /** @var FilterExtensionVisitor $id */
            $visitorCollection->addMethodCall('addVisitor', [
                $id::driver(),
                $facadeExtensionVisitor,
            ]);
        }
    }

}
