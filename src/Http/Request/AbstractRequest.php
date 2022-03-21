<?php

declare(strict_types=1);

namespace DromTask2\Http\Request;

use Closure;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Traversable;

/**
 * Абстрактный класс запроса.
 */
abstract class AbstractRequest
{
    /**
     * Возвращает HTTP метод запроса.
     *
     * @return string
     */
    abstract public function getMethod(): string;

    /**
     * Возвращает URI запроса.
     *
     * @return string
     */
    abstract public function getUri(): string;

    /**
     * Тело запроса.
     *
     * @return array|string|resource|Traversable|Closure
     */
    public function getBody()
    {
        return HttpClientInterface::OPTIONS_DEFAULTS['body'] ?? '';
    }
}
