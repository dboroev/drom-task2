<?php

declare(strict_types=1);

namespace DromTask2\Http\Request;

/**
 * Класс объекта запроса комментариев.
 */
final class GetCommentsRequest extends AbstractRequest
{
    /**
     * Возвращает HTTP метод запроса.
     *
     * @return string
     */
    public function getMethod(): string
    {
        return 'GET';
    }

    /**
     * Возвращает URI запроса.
     *
     * @return string
     */
    public function getUri(): string
    {
        return '/comments';
    }
}
