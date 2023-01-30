<?php

declare(strict_types=1);

namespace Ranky\SharedBundle\Presentation\Behat;

use Symfony\Component\BrowserKit\AbstractBrowser;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

trait ApiContextTrait
{

    /**
     * @throws \JsonException
     */
    public function sendRequest(string $method, string $url): Crawler
    {
        $server  = [];
        $headers = [
            ...[
                'x-requested-with' => 'XMLHttpRequest',
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            ...$this->headers,
        ];


        foreach ($headers as $headerName => $headerValue) {
            if (\strtolower((string)$headerName) === 'content-type') {
                $server['CONTENT_TYPE'] = $headerValue ?? '';
                continue;
            }
            $headerName          = 'HTTP_'.\str_replace('-', '_', \strtoupper((string)$headerName));
            $server[$headerName] = $headerValue;
        }

        return $this->getClient()->request(
            $method,
            $url,
            $this->parameters,
            $this->files,
            $server,
            \json_encode($this->body, \JSON_THROW_ON_ERROR)
        );
    }

    public function getStatusCode(): int
    {
        return $this->getSession()->getStatusCode();
    }

    public function getClient(): AbstractBrowser
    {
        /** @var \Behat\Mink\Driver\BrowserKitDriver $driver */
        $driver = $this->getSession()->getDriver();

        return $driver->getClient();
    }

    /**
     * @param UserInterface $user
     * @param string $firewallContext
     * @throws \Exception
     * @return void
     */
    public function loginUser(UserInterface $user, string $firewallContext = 'main'): void
    {
        $token = new UsernamePasswordToken($user, $firewallContext, $user->getRoles());
        /** @var \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage $tokenStorage */
        $tokenStorage = self::$container->get('security.untracked_token_storage');
        $tokenStorage->setToken($token);
        /** @var \Symfony\Component\HttpFoundation\Session\SessionFactory $sessionFactory */
        $sessionFactory =  self::$container->get('session.factory');
        /** @var \Symfony\Component\HttpFoundation\Session\Session $session */
        $session = $sessionFactory->createSession();
        $session->set('_security_'.$firewallContext, serialize($token));
        $session->save();
        $this->getSession()->setCookie($session->getName(), $session->getId());
    }

    public function logout(string $firewallContext = 'main'): void
    {
        /** @var \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage $tokenStorage */
        $tokenStorage = self::$container->get('security.untracked_token_storage');
        $tokenStorage->setToken();
        /** @var \Symfony\Component\HttpFoundation\Session\Session $session */
        $session = self::$container->get('session');
        $session->set('_security_'.$firewallContext, null);
        $session->save();
        $this->getClient()->getCookieJar()->clear();
    }

    /**
     * @throws \Exception
     */
    public function isGranted(string $role = 'ROLE_USER'): bool
    {
        /** @var AuthorizationCheckerInterface $authorizationChecker */
        $authorizationChecker = self::$container->get(AuthorizationCheckerInterface::class);

        return $authorizationChecker->isGranted($role);
    }

    /**
     * @return object | \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest(): object
    {
        return $this->getClient()->getRequest();
    }

    /**
     * @return object | \Symfony\Component\HttpFoundation\Response
     */
    public function getResponse(): object
    {
        return $this->getClient()->getResponse();
    }

    public function getResponseContent(): string
    {
        /** @var  \Symfony\Component\HttpFoundation\Response $response */
        $response = $this->getResponse();

        return $response->getContent() ?: '';
    }

    /**
     * @throws \JsonException
     * @return array<string, mixed>
     */
    public function getJSONResponseContentAsArray(): array
    {
        return \json_decode($this->getResponseContent(), true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * @return array<string, mixed>
     */
    public function getResponseHeaders(): array
    {
        return $this->getSession()->getResponseHeaders();
    }

    public function resetSession(): void
    {
        $this->getSession()->reset();
    }

    /**
     * @throws \JsonException
     */
    public function json(string $content): string
    {
        return \json_encode(
            \json_decode(\trim($content), true, 512, JSON_THROW_ON_ERROR),
            JSON_THROW_ON_ERROR
        );
    }

    /**
     * @throws \JsonException
     * @return array<string, mixed>
     */
    public function jsonRawToArray(string $content): array
    {
        // trim(preg_replace('/\s+/', '', $content))
        return \json_decode(\trim($content), true, 512, JSON_THROW_ON_ERROR);
    }

    public function normalizeJsonExpression(string $expression): string
    {
        return \str_replace('response.', '', $expression);
    }

    public function normalizeInput(string $regexValue): string
    {
        return \str_replace(['"', "'"], '', $regexValue);
    }

}
