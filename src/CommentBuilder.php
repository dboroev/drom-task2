<?php

declare(strict_types=1);

namespace DromTask2;

use DromTask2\Model\Comment;
use DromTask2\Model\CommentBody;
use stdClass;
use Webmozart\Assert\Assert;

/**
 * Класс билдер комментария.
 */
final class CommentBuilder
{
    /**
     * Инстанцирует объект комментария из json строки.
     *
     * @param string $data данные json.
     *
     * @return Comment
     */
    public static function buildFromJson(string $data): Comment
    {
        $data = json_decode($data, true, JSON_THROW_ON_ERROR);

        Assert::notEmpty($data['id']);
        Assert::notEmpty($data['name']);
        Assert::notEmpty($data['text']);

        return new Comment(
            $data['id'],
            new CommentBody($data['name'], $data['text'])
        );
    }

    /**
     * Инстанцирует объект комментария из объекта stdClass.
     *
     * @param stdClass $data данные.
     *
     * @return Comment
     */
    public static function buildFromStd(stdClass $data): Comment
    {
        Assert::propertyExists($data, 'id');
        Assert::propertyExists($data, 'name');
        Assert::propertyExists($data, 'text');

        return new Comment(
            $data->id,
            new CommentBody($data->name, $data->text)
        );
    }
}
