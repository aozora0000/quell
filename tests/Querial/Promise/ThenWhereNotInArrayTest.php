<?php

namespace Tests\Querial\Promise;

use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\Test;
use Querial\Promise\ThenWhereNotInArray;
use Tests\Querial\WithEloquentModelTestCase;

class ThenWhereNotInArrayTest extends WithEloquentModelTestCase
{
    #[Test]
    public function リクエストにキーが存在し単体の場合_where_inを発行する事を確認(): void
    {
        $request = Request::create('/', 'GET', ['name' => ['test1'], 'email' => 'email@email.com']);
        $model = $this->createModel();
        $query = $model->newQuery();

        $query = (new ThenWhereNotInArray('name'))->resolve($request, $query);

        $sql = <<<'EOT'
SELECT
  *
FROM
  `users`
WHERE
  `users`.`name` NOT IN ('test1')
EOT;
        $this->assertSame(mb_strtolower($sql), $this->format($query));
    }

    #[Test]
    public function リクエストにキーが存在し配列の場合_where_inを発行する事を確認(): void
    {
        $request = Request::create('/', 'GET', ['name' => ['test1', 'test2'], 'email' => 'email@email.com']);
        $model = $this->createModel();
        $query = $model->newQuery();

        $query = (new ThenWhereNotInArray('name'))->resolve($request, $query);

        $sql = <<<'EOT'
SELECT
  *
FROM
  `users`
WHERE
  `users`.`name` NOT IN ('test1', 'test2')
EOT;
        $this->assertSame(mb_strtolower($sql), $this->format($query));
    }
}
