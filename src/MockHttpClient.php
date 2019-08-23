<?php


namespace BlastCloud\Hybrid;

use Symfony\Component\HttpClient\Exception\TransportException;
use Symfony\Component\HttpClient\MockHttpClient as Base;
use Symfony\Contracts\HttpClient\ResponseInterface;

class MockHttpClient extends Base
{
    public function request(string $method, string $url, array $options = []): ResponseInterface
    {
        if (($options['body'] ?? null) instanceof \Generator) {
            $body = $options['body'];
            $result = '';

            foreach ($body as $data) {
                if (!\is_string($data)) {
                    throw new TransportException(sprintf('Return value of the "body" option callback must be string, %s returned.', \gettype($data)));
                }

                $result .= $data;
            }

            $options['body'] = $result;
        }

        return parent::request($method, $url, $options);
    }
}