<?php

declare(strict_types=1);

namespace DromTask2\Exception;

use RuntimeException;

/**
 * Исключение сервиса комментариев.
 */
final class CommentServiceException extends RuntimeException
{
    /**
     * Генерирует исключение. Вызывает при коде ответа отличном от 200.
     *
     * @param int    $statusCode Код ответа, полученного от сервиса комментариев.
     * @param string $content    Тело ответа.
     *
     * @return self
     */
    public static function failResponse(int $statusCode, string $content): self
    {
        $message = 'Ошибка отправки запроса в сервис комментариев. Код ответа "%d". Тело ответа "%s".';

        return new self(sprintf($message, $statusCode, $content));
    }
}
