<?php

declare(strict_types=1);

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Ranky\SharedBundle\Tests\TestKernel;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Dotenv\Dotenv;

require __DIR__.'/../vendor/autoload.php';

DG\BypassFinals::enable();

(new Dotenv())->bootEnv(__DIR__.'/.env');

// Create and boot 'test' kernel
$kernel = new TestKernel('test', (bool)$_ENV['APP_DEBUG']);
$kernel->boot();

// Create new application console
$application = new Application($kernel);
$application->setAutoExit(false);

$application->run(
    new ArrayInput([
        'command' => 'cache:clear',
        '--no-warmup' => true,
        '--env' => 'test',
    ]),
    new ConsoleOutput()
);

$application->run(
    new ArrayInput([
        'command' => 'doctrine:database:drop',
        '--if-exists' => true,
        '--force' => true,
    ]),
    new ConsoleOutput()
);

$application->run(
    new ArrayInput([
        'command' => 'doctrine:database:create',
    ]),
    new ConsoleOutput()
);

$application->run(
    new ArrayInput([
        'command' => 'doctrine:schema:update',
        '--force' => true,
        '--complete' => true,
    ]),
    new ConsoleOutput()
);

$application->run(
    new ArrayInput([
        'command' => 'doctrine:fixtures:load',
        '--no-interaction' => true,
        '--env' => 'test',
    ])
);
