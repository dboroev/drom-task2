<?php

declare(strict_types=1);

namespace DromTask2\Http\Request;

use Closure;
use Traversable;

/**
 * Класс объекта запроса редактирования комментария.
 */
final class UpdateCommentRequest extends AbstractRequest
{
    /**
     * @param int         $id   Идентификатор комментария.
     * @param string|null $name Имя автора комментария.
     * @param string|null $text Текст комментария
     */
    public function __construct(private int $id, private ?string $name = null, private ?string $text = null)
    {
    }

    /**
     * Устанавливает имя.
     *
     * @param string|null $name Имя.
     *
     * @return self
     */
    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Устанавливает текст.
     *
     * @param string|null $text Текст.
     *
     * @return self
     */
    public function setText(?string $text): self
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Возвращает HTTP метод запроса.
     *
     * @return string
     */
    public function getMethod(): string
    {
        return 'PUT';
    }

    /**
     * Возвращает URI запроса.
     *
     * @return string
     */
    public function getUri(): string
    {
        return '/comment/' . $this->id;
    }

    /**
     * Тело запроса.
     *
     * @return array|string|resource|Traversable|Closure
     */
    public function getBody()
    {
        $body = [];
        if (isset($this->name)) {
            $body['name'] = $this->name;
        }

        if (isset($this->text)) {
            $body['text'] = $this->text;
        }

        return !empty($body)
            ? $body
            : parent::getBody();
    }
}
