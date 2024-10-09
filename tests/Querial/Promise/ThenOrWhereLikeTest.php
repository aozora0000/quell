<?php

namespace Tests\Querial\Promise;

use Illuminate\Http\Request;
use Querial\Promise\ThenOrWhereLike;
use Tests\Querial\WithEloquentModelTestCase;

class ThenOrWhereLikeTest extends WithEloquentModelTestCase
{
    /**
     * @test
     */
    public function 複数のWhereクエリが入った時にORになる(): void
    {
        $request = Request::create('/', 'GET', ['name' => 'test', 'email' => 'email@email.com']);
        $model = $this->createModel();
        $query = $model->newQuery();

        // 検索するテーブルを指定してクエリを作成する
        $query = (new ThenOrWhereLike('name'))->resolve($request, $query);
        $sql = <<<'EOT'
SELECT
  *
FROM
  "users"
WHERE
  "users"."name" LIKE '%test%'
EOT;
        $this->assertSame(mb_strtolower($sql), $this->format($query));

        // 検索するテーブルを指定してクエリを作成する
        $query = (new ThenOrWhereLike('email'))->resolve($request, $query);
        $sql = <<<'EOT'
SELECT
  *
FROM
  "users"
WHERE
  "users"."name" LIKE '%test%'
  OR "users"."email" LIKE '%email@email.com%'
EOT;
        $this->assertSame(mb_strtolower($sql), $this->format($query));
    }
}
