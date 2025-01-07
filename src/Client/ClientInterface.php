<?php declare(strict_types=1);

namespace App\Client;

use Symfony\Component\HttpFoundation\JsonResponse;

interface ClientInterface
{
    /**
     * @param array{objectvalue?: mixed} $parameters
     */
    public function get(string $endpoint, array $parameters = []): JsonResponse;
}
