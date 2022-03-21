<?php

namespace DromTask2;

use DromTask2\Model\Comment;
use DromTask2\Model\CommentBody;

/**
 * Интерфейс сервиса комментариев.
 */
interface CommentServiceInterface
{
    /**
     * Возвращает комментарии.
     *
     * @return iterable
     */
    public function getComments(): iterable;

    /**
     * Создает комментарий.
     *
     * @param CommentBody $commentBody Тело комментария (автор и текст).
     *
     * @return Comment|null
     */
    public function createComment(CommentBody $commentBody): ?Comment;

    /**
     * Редактирует комментарий.
     *
     * @param Comment $comment Объект редактируемого комментария.
     * @param array   $data    Редактируемые данные.
     *
     * @return Comment|null
     */
    public function updateComment(Comment $comment, array $data): ?Comment;
}
