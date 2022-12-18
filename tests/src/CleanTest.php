<?php

declare(strict_types=1);


namespace Ranky\SharedBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;

class CleanTest extends BaseIntegrationTestCase
{

    public static function testItShouldRemoveDatabase(): void
    {
        /** @var \Symfony\Component\HttpKernel\KernelInterface $kernel */
        $kernel = self::getContainer()->get('kernel');
        $application = new Application($kernel);
        $application->setAutoExit(false);
        $application->run(
            new ArrayInput([
                'command'     => 'doctrine:database:drop',
                '--if-exists' => true,
                '--force'     => true,
            ])
        );

        self::assertTrue(true);
    }

}
