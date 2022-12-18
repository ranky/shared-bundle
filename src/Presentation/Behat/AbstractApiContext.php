<?php
declare(strict_types=1);

namespace Ranky\SharedBundle\Presentation\Behat;


use Behat\Gherkin\Node\PyStringNode;
use Behat\MinkExtension\Context\RawMinkContext;
use PHPUnit\Framework\Assert;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpKernel\KernelInterface;

use function JmesPath\search as JSONSearch;


abstract class AbstractApiContext extends RawMinkContext
{
    use ApiContextTrait;

    protected Crawler $crawler;
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

    /**
     * @Transform /^(\d+|"\d+")$/
     * @link https://behat.org/en/latest/user_guide/context/definitions.html#step-argument-transformations
     */
    public function castStringToNumber(string $string): int
    {
        return (int)$this->normalizeInput($string);
    }

    /**
     * @Given /^I set "([^"]*)" header equal to "([^"]*)"$/
     */
    public function iSetHeaderEqualTo(string $headerName, string $headerValue): void
    {
        $this->headers[$headerName] = $headerValue;
    }

    /**
     * @When I send a :method request to :url
     */
    public function iSendRequestTo(string $method, string $url): void
    {
        $this->crawler = $this->sendRequest($method, $url);
    }

    /**
     * @When I send a :method request to :url with body:
     * @throws \JsonException
     */
    public function iSendRequestWithBody(string $method, string $url, PyStringNode $body): void
    {
        $this->body = $this->jsonRawToArray($body->getRaw());
        $this->iSendRequestTo($method, $url);
    }

    /**
     * @When I send a :method request to :url with parameters:
     * @throws \JsonException
     */
    public function iSendRequestWithParameters(string $method, string $url, PyStringNode $parameters): void
    {
        $this->parameters = $this->jsonRawToArray($parameters->getRaw());
        $this->iSendRequestTo($method, $url);
    }


    /**
     * @Then the response status code should be :statusCode
     */
    public function theResponseStatusCodeShouldBe(string $statusCode): void
    {
        if ((int)$statusCode !== $this->getStatusCode()) {
            throw new \RuntimeException(
                sprintf(
                    'Expected to receive a status code of %d, but received %d.',
                    $statusCode,
                    $this->getStatusCode()
                )
            );
        }
    }

    /**
     * @Then /^the response header "([^"]*)" should be equal to "([^"]*)"$/
     */
    public function theResponseHeaderShouldBeEqualTo(string $headerName, string $headerValue): void
    {
        /** @var \Symfony\Component\HttpFoundation\Response $response */
        $response = $this->getResponse();
        Assert::assertTrue(
            $response->headers->has($headerName)
            && $response->headers->get($headerName) === $headerValue
        );
    }

    /**
     * @Then /^the response should be valid JSON expression "([^"]+)"$/
     *
     * @throws \JsonException
     */
    public function theJSONExpressionShouldBeValid(string $expression): void
    {
        $response = $this->getJSONResponseContentAsArray();
        $search   = JSONSearch($this->normalizeJsonExpression($expression), $response);
        Assert::assertNotEmpty(\is_array($search) ? \array_filter($search) : $search);
        Assert::assertNotNull($search);
    }

    /**
     * @Then /^the response JSON expression match "([^"]+)" == "([^"]+)"$/
     * @Then /^the response JSON expression match "([^"]+)" == "([^"]+)" as "([^"]+)"$/
     * @Then /^the response JSON expression match "([^"]+)" contains "([^"]+)"$/
     * @Then /^the response JSON expression match "([^"]+)" contains "([^"]+)" as "([^"]+)"$/
     *
     */
    public function theJSONExpressionMatchEqualTo(string $expression, string $content, string $type = null): void
    {
        [$search, $content] = $this->theJSONExpressionMatch($expression, $content, $type);

        if (\is_array($search)) {
            Assert::assertContains($content, $search);
        } else {
            Assert::assertSame($search, $content);
        }
    }

    /**
     * @throws \JsonException
     * @return array<int, mixed>
     */
    protected function theJSONExpressionMatch(string $expression, string $content, string $type = null): array
    {
        $response = $this->getJSONResponseContentAsArray();
        $search   = JSONSearch(
            $this->normalizeJsonExpression($this->normalizeInput($expression)),
            $response
        );
        $content  = $this->normalizeInput($content);
        if ($type) {
            \settype($content, $this->normalizeInput($type));
        }

        return [
            $search,
            $content,
        ];
    }


    /**
     * @Then the JSON response key :key should include:
     * @throws \JsonException
     */
    public function theJSONResponseKeyShouldBeInclude(string $key, PyStringNode $content): void
    {
        $response = $this->getJSONResponseContentAsArray();
        $data     = $this->jsonRawToArray($content->getRaw());
        $search   = JSONSearch($this->normalizeJsonExpression($key), $response);
        $compare  = array_diff_assoc(array_filter($data), array_filter($search));
        Assert::assertSame(count($compare), 0);
    }

    /**
     * @Then the JSON response key :key should exist
     * @throws \JsonException
     */
    public function theJSONResponseKeyShouldBeExist(string $key): void
    {
        $response = $this->getJSONResponseContentAsArray();
        Assert::assertTrue(\array_key_exists($key, $response));
    }

    /**
     * @Then the JSON response key :key should be empty
     * @throws \JsonException
     */
    public function theJSONResponseKeyShouldBeEmpty(string $key): void
    {
        $response = $this->getJSONResponseContentAsArray();
        Assert::assertTrue(\array_key_exists($key, $response) && empty($response[$key]));
    }

    /**
     * @Then the JSON response key :key should be equal to:
     * @throws \JsonException
     */
    public function theJSONResponseKeyShouldBeEqualTo(string $key, PyStringNode $content): void
    {
        $response = $this->getJSONResponseContentAsArray();
        $data     = $this->jsonRawToArray($content->getRaw());
        $key      = $this->normalizeJsonExpression($key);
        Assert::assertEquals($data, $key ?: $response);
    }


    /**
     * @Then the JSON response content should be:
     * @throws \JsonException
     */
    public function theJSONResponseContentShouldBe(PyStringNode $content): void
    {
        $response = $this->getJSONResponseContentAsArray();
        $data     = $this->jsonRawToArray($content->getRaw());

        Assert::assertEquals($data, $response);
    }

    /**
     * @Then the response should be a valid JSON
     * @throws \JsonException
     */
    public function theResponseShouldBeAValidJSON(): void
    {
        $this->getJSONResponseContentAsArray();
    }

    /**
     * @Then dump request
     */
    public function dumpRequest(): void
    {
        dump('Request', $this->getRequest());
    }

    /**
     * @Then dump response
     */
    public function dumpResponse(): void
    {
        dump('Response', $this->getResponse());
    }

}

