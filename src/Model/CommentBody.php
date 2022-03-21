<?php

declare(strict_types=1);

namespace DromTask2\Model;

/**
 * Контейнер тела комментария.
 */
final class CommentBody
{
    /**
     * @param string $name имя.
     * @param string $text текст.
     */
    public function __construct(private string $name, private string $text)
    {
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }
}
