<?php

declare(strict_types=1);


namespace Ranky\SharedBundle\Presentation\Behat;


use Behat\MinkExtension\Context\RawMinkContext;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpKernel\KernelInterface;

class BaseApiContext extends RawMinkContext
{
    use ApiContextTrait;

    public Crawler $crawler;
    public static ContainerInterface $container;
    public static KernelInterface $kernel;
    /**
     * @var array<string, mixed>
     */
    protected array $headers = [];
    /**
     * @var array<string,mixed>
     */
    protected array $files = [];
    /**
     * @var array<string,mixed>
     */
    protected array $body = [];
    /**
     * @var array<string,mixed>
     */
    protected array $parameters = [];

    public function __construct(KernelInterface $kernel)
    {
        self::$kernel    = $kernel;
        /** @phpstan-ignore-next-line */
        self::$container = $kernel->getContainer()->get('test.service_container');
    }

}
