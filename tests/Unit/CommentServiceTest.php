<?php

declare(strict_types=1);

namespace DromTask2Test\Unit;

use DromTask2\CommentService;
use DromTask2\Http\CommentHttpClient;
use DromTask2\Model\Comment;
use DromTask2\Model\CommentBody;
use Generator;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

/**
 * Тестирование сервиса комментариев.
 */
final class CommentServiceTest extends TestCase
{
    /**
     * Тестирование получения комментариев.
     *
     * @param array $responseBody тело ответа.
     * @param array $expected     ожидаемые данные.
     *
     * @dataProvider getDataForTestGetComments
     */
    public function testGetComments(int $httpCode, array $responseBody, bool $isValidGenerator, array $expected): void
    {
        $commentService = $this->getCommentService($httpCode, $responseBody);

        $comments = $commentService->getComments();
        if (! $comments instanceof Generator) {
            $this->fail('Ожидается объект Generator.');
        }

        $this->assertEquals($isValidGenerator, $comments->valid());
        if (! $comments->valid()) {
            return;
        }

        foreach ($comments as $comment) {
            $this->assertInstanceOf(Comment::class, $comment);
            $this->assertEquals($expected[$comment->getId()], $comment);
        }
    }

    /**
     * Дата-провайдер тестирования получения комментариев.
     *
     * @return array[]
     */
    public function getDataForTestGetComments(): array
    {
        return [
            [
                'http_code'          => 200,
                'response_body'      => [
                    [
                        'id'   => 1,
                        'name' => 'name1',
                        'text' => 'text1',
                    ],
                    [
                        'id'   => 2,
                        'name' => 'name2',
                        'text' => 'text2',
                    ]
                ],
                'is_valid_generator' => true,
                'expected'           => [
                    1 => new Comment(1, new CommentBody('name1', 'text1')),
                    2 => new Comment(2, new CommentBody('name2', 'text2')),
                ],
            ],
            [
                'http_code'          => 200,
                'response_body'      => [
                    [
                        'name' => 'name1',
                        'text' => 'text1',
                    ],
                ],
                'is_valid_generator' => false,
                'expected'           => [],
            ],
            [
                'http_code'          => 401,
                'response_body'      => [],
                'is_valid_generator' => false,
                'expected'           => [],
            ],
        ];
    }


    /**
     * Тестирование создания комментария.
     *
     * @param int          $httpCode     http код.
     * @param CommentBody  $createData   данные для создания комментария.
     * @param array        $responseBody тело ответа.
     * @param Comment|null $expected     ожидаемый комментарий.
     *
     * @dataProvider getDataForTestCreateComment
     */
    public function testCreateComment(int $httpCode, CommentBody $createData, array $responseBody, ?Comment $expected): void
    {
        $commentService = $this->getCommentService($httpCode, $responseBody);

        $this->assertEquals($expected, $commentService->createComment($createData));
    }

    /**
     * Дата-провайдер создания комментария.
     *
     * @return array[]
     */
    public function getDataForTestCreateComment(): array
    {
        return [
            [
                'http_code'     => 201,
                'create_data'   => new CommentBody('name1', 'text1'),
                'response_body' => [
                    'id'   => 1,
                    'name' => 'name1',
                    'text' => 'text1',
                ],
                'expected'      => new Comment(1, new CommentBody('name1', 'text1')),
            ],
            [
                'http_code'     => 201,
                'create_data'   => new CommentBody('name1', 'text1'),
                'response_body' => [
                    'name' => 'name1',
                    'text' => 'text1',
                ],
                'expected'      => null,
            ],
            [
                'http_code'     => 403,
                'create_data'   => new CommentBody('name1', 'text1'),
                'response_body' => [],
                'expected'      => null,
            ],
        ];
    }

    /**
     * Тестирование редактирования комментария.
     *
     * @param int          $httpCode     http код.
     * @param Comment      $comment      редактируемый комментарий.
     * @param array        $updateData   данные для обовления.
     * @param array        $responseBody тело ответа.
     * @param Comment|null $expected     ожидаемый комментарий.
     *
     * @dataProvider getDataForTestUpdateComment
     */
    public function testUpdateComment(
        int $httpCode,
        Comment $comment,
        array $updateData,
        array $responseBody,
        ?Comment $expected
    ): void {
        $commentService = $this->getCommentService($httpCode, $responseBody);

        $this->assertEquals($expected, $commentService->updateComment($comment, $updateData));
    }

    /**
     * Дата-провайдер редактирования комментария.
     *
     * @return array[]
     */
    public function getDataForTestUpdateComment(): array
    {
        return [
            [
                'http_code'     => 201,
                'comment'       => new Comment(1, new CommentBody('name1', 'text1')),
                'update__data'  => [
                    'name' => 'name2',
                    'text' => 'text2',
                ],
                'response_body' => [
                    'id'   => 1,
                    'name' => 'name2',
                    'text' => 'text2',
                ],
                'expected'      => new Comment(1, new CommentBody('name2', 'text2')),
            ],
            [
                'http_code'     => 201,
                'comment'       => new Comment(1, new CommentBody('name1', 'text1')),
                'update__data'  => [],
                'response_body' => [],
                'expected'      => new Comment(1, new CommentBody('name1', 'text1')),
            ],
            [
                'http_code'     => 201,
                'comment'       => new Comment(1, new CommentBody('name1', 'text1')),
                'update__data'  => [
                    'name' => 'name2',
                    'text' => 'text2',
                ],
                'response_body' => [
                    'name' => 'name2',
                    'text' => 'text2',
                ],
                'expected'      => null,
            ],
            [
                'http_code'     => 201,
                'comment'       => new Comment(1, new CommentBody('name1', 'text1')),
                'update__data'  => [
                    'name' => 'name1',
                    'text' => 'text1',
                ],
                'response_body' => [],
                'expected'      => new Comment(1, new CommentBody('name1', 'text1')),
            ],
            [
                'http_code'     => 403,
                'comment'       => new Comment(1, new CommentBody('name1', 'text1')),
                'update__data'  => [
                    'name' => 'name2',
                    'text' => 'text2',
                ],
                'response_body' => [],
                'expected'      => null,
            ],
        ];
    }

    /**
     * Возвращает сервис комментариев.
     *
     * @param int   $httpCode     http код.
     * @param array $responseBody тело ответа.
     *
     * @return CommentService
     */
    private function getCommentService(int $httpCode, array $responseBody): CommentService
    {
        $httpClient = new MockHttpClient(
            static function () use($responseBody, $httpCode) {
                return new MockResponse(
                    json_encode($responseBody),
                    ['http_code' => $httpCode]
                );
            }
        );

        return new CommentService(
            new CommentHttpClient($httpClient),
            $this->createMock(LoggerInterface::class)
        );
    }
}
