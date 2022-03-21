<?php

declare(strict_types=1);

namespace DromTask2\Model;

/**
 * Контейнер комментария.
 */
final class Comment
{
    /**
     * @param int         $id          идентификатор.
     * @param CommentBody $commentBody тело комментария.
     */
    public function __construct(private int $id, private CommentBody $commentBody)
    {
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->commentBody->getName();
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->commentBody->getText();
    }
}
