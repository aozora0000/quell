<?php

namespace Tests\Querial\Promise;

use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\Test;
use Querial\Promise\ThenWhereInArray;
use Tests\Querial\WithEloquentModelTestCase;

class ThenWhereInArrayTest extends WithEloquentModelTestCase
{
    /**
     * @test
     */
    #[Test]
    public function リクエストにキーが存在し単体の場合_where_inを発行する事を確認(): void
    {
        $request = Request::create('/', 'GET', ['name' => ['test1'], 'email' => 'email@email.com']);
        $model = $this->createModel();
        $query = $model->newQuery();

        $query = (new ThenWhereInArray('name'))->resolve($request, $query);
        $sql = <<<'EOT'
SELECT
  *
FROM
  "users"
WHERE
  "users"."name" IN ('test1')
EOT;
        $this->assertSame(mb_strtolower($sql), $this->format($query));
    }

    /**
     * @test
     */
    #[Test]
    public function リクエストにキーが存在し配列の場合_where_inを発行する事を確認(): void
    {
        $request = Request::create('/', 'GET', ['name' => ['test1', 'test2'], 'email' => 'email@email.com']);
        $model = $this->createModel();
        $query = $model->newQuery();

        $query = (new ThenWhereInArray('name'))->resolve($request, $query);
        $sql = <<<'EOT'
SELECT
  *
FROM
  "users"
WHERE
  "users"."name" IN ('test1', 'test2')
EOT;
        $this->assertSame(mb_strtolower($sql), $this->format($query));
    }
}
