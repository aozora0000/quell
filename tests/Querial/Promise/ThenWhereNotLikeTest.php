<?php

namespace Tests\Querial\Promise;

use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\Test;
use Querial\Formatter\LikeFormatter;
use Querial\Promise\ThenWhereNotLike;
use Tests\Querial\WithEloquentModelTestCase;

class ThenWhereNotLikeTest extends WithEloquentModelTestCase
{
    #[Test]
    public function 無指定の場合、部分一致としてlike検索される(): void
    {
        $request = Request::create('/', 'GET', ['name' => 'test', 'email' => 'email@email.com']);
        $model = $this->createModel();
        $query = $model->newQuery();

        $query = (new ThenWhereNotLike('name'))->resolve($request, $query);

        $sql = <<<'EOT'
SELECT
  *
FROM
  `users`
WHERE
  `users`.`name` NOT LIKE '%test%'
EOT;
        $this->assertSame(mb_strtolower($sql), $this->format($query));
    }

    #[Test]
    public function 指定された場合、後方一致としてlike検索される(): void
    {
        $request = Request::create('/', 'GET', ['name' => 'test', 'email' => 'email@email.com']);
        $model = $this->createModel();
        $query = $model->newQuery();

        $query = (new ThenWhereNotLike('email', null, null, LikeFormatter::BACKWARD_MATCH))->resolve($request, $query);

        $sql = <<<'EOT'
SELECT
  *
FROM
  `users`
WHERE
  `users`.`email` NOT LIKE '%email@email.com'
EOT;
        $this->assertSame(mb_strtolower($sql), $this->format($query));
    }

    #[Test]
    public function 指定された場合、前方一致としてlike検索される(): void
    {
        $request = Request::create('/', 'GET', ['name' => 'test', 'email' => 'email@email.com']);
        $model = $this->createModel();
        $query = $model->newQuery();

        $query = (new ThenWhereNotLike('email', null, null, LikeFormatter::FORWARD_MATCH))->resolve($request, $query);

        $sql = <<<'EOT'
SELECT
  *
FROM
  `users`
WHERE
  `users`.`email` NOT LIKE 'email@email.com%'
EOT;
        $this->assertSame(mb_strtolower($sql), $this->format($query));
    }
}
