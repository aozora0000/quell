<?php

namespace Tests\Querial\Promise;

use Illuminate\Http\Request;
use Querial\Promise\ThenWhereNotInArray;
use Tests\Querial\WithEloquentModelTestCase;

class ThenWhereNotInArrayTest extends WithEloquentModelTestCase
{
    /**
     * @test
     * @return void
     */
    public function リクエストにキーが存在し単体の場合WhereInを発行する事を確認(): void
    {
        $request = Request::create('/', 'GET', ['name' => ['test1'], 'email' => 'email@email.com']);
        $model = $this->createModel();
        $query = $model->newQuery();

        $query = (new ThenWhereNotInArray('name'))->resolve($request, $query);
        $sql = <<<'EOT'
SELECT
  *
FROM
  "users"
WHERE
  "users"."name" NOT IN ('test1')
EOT;
        $this->assertSame($sql, $this->format($query));
    }

    /**
     * @test
     * @return void
     */
    public function リクエストにキーが存在し配列の場合WhereInを発行する事を確認(): void
    {
        $request = Request::create('/', 'GET', ['name' => ['test1', 'test2'], 'email' => 'email@email.com']);
        $model = $this->createModel();
        $query = $model->newQuery();

        $query = (new ThenWhereNotInArray('name'))->resolve($request, $query);
        $sql = <<<'EOT'
SELECT
  *
FROM
  "users"
WHERE
  "users"."name" NOT IN ('test1', 'test2')
EOT;
        $this->assertSame($sql, $this->format($query));
    }
}
