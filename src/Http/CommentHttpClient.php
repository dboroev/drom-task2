<?php

declare(strict_types=1);

namespace DromTask2\Http;

use DromTask2\Http\Request\AbstractRequest;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Contracts\HttpClient\ResponseStreamInterface;

/**
 * Класс http клиента сервиса комментариев.
 */
class CommentHttpClient
{
    /**
     * @param HttpClientInterface $httpClient http клиент.
     */
    public function __construct(private HttpClientInterface $httpClient)
    {
    }

    /**
     * Выполняет http запрос.
     *
     * @param AbstractRequest $request абстрактный запрос.
     *
     * @return ResponseInterface
     *
     * @throws TransportExceptionInterface
     */
    public function execute(AbstractRequest $request): ResponseInterface
    {
        return $this->httpClient->request(
            $request->getMethod(),
            $request->getUri(),
            [
                'body' => $request->getBody(),
            ]
        );
    }

    /**
     * Выдает ответы по частям по мере их завершения.
     *
     * @param ResponseInterface $response http ответ.
     */
    public function stream(ResponseInterface $response): ResponseStreamInterface
    {
        return $this->httpClient->stream($response);
    }
}
