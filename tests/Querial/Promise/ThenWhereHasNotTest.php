<?php

namespace Tests\Querial\Promise;

use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\Test;
use Querial\Promise\Support\ThenPromisesAggregator;
use Querial\Promise\ThenWhereEqual;
use Querial\Promise\ThenWhereHasNot;
use Tests\Querial\WithEloquentModelTestCase;

class ThenWhereHasNotTest extends WithEloquentModelTestCase
{
    /**
     * @test
     */
    #[Test]
    public function リクエストにキーが存在する場合_notexists_whereサブクエリを発行する事を確認(): void
    {
        $request = Request::create('/', 'GET', ['name' => 'test', 'email' => 'email@email.com']);
        $model = $this->createModel();
        $query = $model->newQuery();

        $query = (new ThenWhereHasNot('items'))->resolve($request, $query);
        $sql = <<<'EOT'
SELECT
  *
FROM
  "users"
WHERE
  NOT EXISTS (
    SELECT
      *
    FROM
      "items"
    WHERE
      "users"."id" = "items"."user_id"
  )
EOT;
        $this->assertSame(mb_strtolower($sql), $this->format($query));
    }

    /**
     * @test
     */
    #[Test]
    public function 複数の_not_existsサブクエリが入った時に_an_dになる(): void
    {
        $request = Request::create('/', 'GET', ['name' => 'test', 'email' => 'email@email.com']);
        $model = $this->createModel();
        $query = $model->newQuery();

        $query = (new ThenWhereHasNot('items', new ThenPromisesAggregator([
            new ThenWhereEqual('name', null, 'users'),
        ])))->resolve($request, $query);
        $sql = <<<'EOT'
SELECT
  *
FROM
  "users"
WHERE
  NOT EXISTS (
    SELECT
      *
    FROM
      "items"
    WHERE
      "users"."id" = "items"."user_id"
      AND "users"."name" = 'test'
  )
EOT;
        $this->assertSame(mb_strtolower($sql), $this->format($query));
    }
}
