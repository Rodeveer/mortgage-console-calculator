<?php declare(strict_types=1);

namespace App\Client;

use RuntimeException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class MortgageClient implements ClientInterface
{
    public function __construct(
        protected string $baseUrl,
        protected readonly HttpClientInterface $client,
    )
    {
    }

    /**
     * @inheritDoc
     */
    public function get(string $endpoint, ?array $parameters = null): JsonResponse
    {
        try {
            $response = $this->client->request(
                'GET',
                $this->baseUrl . $endpoint . (!$parameters ?: '?' . http_build_query($parameters))
            );

            return new JsonResponse($response->getContent(), $response->getStatusCode(), $response->getHeaders());
        } catch (TransportExceptionInterface|ClientExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
