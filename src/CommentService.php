<?php

declare(strict_types=1);

namespace DromTask2;

use DromTask2\Exception\CommentServiceException;
use DromTask2\Http\CommentHttpClient;
use DromTask2\Http\Request\CreateCommentRequest;
use DromTask2\Http\Request\UpdateCommentRequest;
use DromTask2\Model\Comment;
use DromTask2\Http\Request\GetCommentsRequest;
use DromTask2\Model\CommentBody;
use Generator;
use JsonMachine\Items;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\ResponseStreamInterface;
use Throwable;

/**
 * Класс http сервиса комментариев.
 */
final class CommentService implements CommentServiceInterface
{
    /**
     * @param CommentHttpClient $commentHttpClient http клиент.
     * @param LoggerInterface   $logger            логгер.
     */
    public function __construct(private CommentHttpClient $commentHttpClient, private LoggerInterface $logger)
    {
    }

    /**
     * Возвращает комментарии.
     *
     * @return iterable
     */
    public function getComments(): iterable
    {
        $request = new GetCommentsRequest();

        try {
            $response = $this->commentHttpClient->execute($request);
            if ($response->getStatusCode() !== 200) {
                throw CommentServiceException::failResponse($response->getStatusCode(), $response->getContent());
            }

            $jsonChunks = $this->httpClientChunks($this->commentHttpClient->stream($response));

            foreach (Items::fromIterable($jsonChunks) as $item) {
                yield CommentBuilder::buildFromStd($item);
            }
        } catch (Throwable $exception) {
            $this->logger->error(
                'Ошибка запроса комментариев.',
                compact('request', 'exception')
            );
        }

        return [];
    }

    /**
     * Генерирует чанки.
     *
     * @param ResponseStreamInterface $responseStream фрагменты ответа.
     *
     * @return Generator
     */
    private function httpClientChunks(ResponseStreamInterface $responseStream): Generator
    {
        foreach ($responseStream as $chunk) {
            yield $chunk->getContent();
        }
    }

    /**
     * Создает комментарий.
     *
     * @param CommentBody $commentBody Тело комментария (автор и текст).
     *
     * @return Comment|null
     */
    public function createComment(CommentBody $commentBody): ?Comment
    {
        $request = new CreateCommentRequest($commentBody->getName(), $commentBody->getText());

        try {
            $response = $this->commentHttpClient->execute($request);
            if ($response->getStatusCode() !== 201) {
                throw CommentServiceException::failResponse($response->getStatusCode(), $response->getContent());
            }

            $comment = CommentBuilder::buildFromJson($response->getContent());
        } catch (Throwable $exception) {
            $this->logger->error(
                'Ошибка создания комментария.',
                compact('request', 'exception')
            );

            return null;
        }

        return $comment;
    }

    /**
     * Редактирует комментарий.
     *
     * @param Comment $comment Объект редактируемого комментария.
     * @param array   $data    Редактируемые данные.
     *
     * @return Comment|null
     */
    public function updateComment(Comment $comment, array $data): ?Comment
    {
        $request = $this->getUpdateCommentRequest($comment, $data);
        if (empty($request->getBody())) {
            return $comment;
        }

        try {
            $response = $this->commentHttpClient->execute($request);
            if ($response->getStatusCode() !== 201) {
                throw CommentServiceException::failResponse($response->getStatusCode(), $response->getContent());
            }

            $comment = CommentBuilder::buildFromJson($response->getContent());
        } catch (Throwable $exception) {
            $this->logger->error(
                'Ошибка редактирования комментария.',
                compact('request', 'exception', 'comment')
            );

            return null;
        }

        return $comment;
    }

    /**
     * Формирует запрос для редактирования комментария.
     *
     * @param Comment $comment комментарий.
     * @param array   $data    данные.
     *
     * @return UpdateCommentRequest
     */
    private function getUpdateCommentRequest(Comment $comment, array $data): UpdateCommentRequest
    {
        $request = new UpdateCommentRequest($comment->getId());

        if (isset($data['name']) && $data['name'] !== $comment->getName()) {
            $request->setName($data['name']);
        }

        if (isset($data['text']) && $data['text'] !== $comment->getText()) {
            $request->setText($data['text']);
        }

        return $request;
    }
}
