<?php

namespace Tests\Querial\Promise;

use Illuminate\Http\Request;
use Querial\Promise\ThenWhereLike;
use Tests\Querial\WithEloquentModelTestCase;

class ThenWhereLikeTest extends WithEloquentModelTestCase
{
    /**
     * @test
     */
    public function リクエストにキーが存在する場合WhereLikeクエリを発行する事を確認(): void
    {
        $request = Request::create('/', 'GET', ['name' => 'test', 'email' => 'email@email.com']);
        $model = $this->createModel();
        $query = $model->newQuery();

        $query = (new ThenWhereLike('name'))->resolve($request, $query);
        $sql = <<<'EOT'
SELECT
  *
FROM
  "users"
WHERE
  "users"."name" LIKE '%test%'
EOT;
        $this->assertSame($sql, $this->format($query));
    }
}
