<?php

namespace Tests\Querial\Promise;

use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\Test;
use Querial\Promise\ThenWhereLike;
use Tests\Querial\WithEloquentModelTestCase;

class ThenWhereLikeTest extends WithEloquentModelTestCase
{
    /**
     * @test
     */
    #[Test]
    public function リクエストにキーが存在する場合_where_likeクエリを発行する事を確認(): void
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
        $this->assertSame(mb_strtolower($sql), $this->format($query));
    }
}
