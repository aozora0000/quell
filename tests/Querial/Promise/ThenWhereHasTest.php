<?php

namespace Tests\Querial\Promise;

use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\Test;
use Querial\Promise\Support\ThenPromisesAggregator;
use Querial\Promise\ThenWhereEqual;
use Querial\Promise\ThenWhereHas;
use Tests\Querial\WithEloquentModelTestCase;

class ThenWhereHasTest extends WithEloquentModelTestCase
{
    #[Test]
    public function リクエストにキーが存在する場合_exists_whereサブクエリを発行する事を確認(): void
    {
        $request = Request::create('/', 'GET', ['name' => 'test', 'email' => 'email@email.com']);
        $model = $this->createModel();
        $query = $model->newQuery();

        $query = (new ThenWhereHas('items'))->resolve($request, $query);

        $sql = <<<'EOT'
SELECT
  *
FROM
  `users`
WHERE
  EXISTS (
    SELECT
      *
    FROM
      `items`
    WHERE
      `users`.`id` = `items`.`user_id`
  )
EOT;
        $this->assertSame(mb_strtolower($sql), $this->format($query));
    }

    #[Test]
    public function 複数の_existsサブクエリが入った時に_an_dになる(): void
    {
        $request = Request::create('/', 'GET', ['name' => 'test', 'email' => 'email@email.com']);
        $model = $this->createModel();
        $query = $model->newQuery();

        $query = (new ThenWhereHas('items', new ThenPromisesAggregator([
            new ThenWhereEqual('name', null, 'users'),
        ])))->resolve($request, $query);
        $query = (new ThenWhereHas('items', new ThenPromisesAggregator([
            new ThenWhereEqual('name', null, 'users'),
        ])))->resolve($request, $query);
        $sql = <<<'EOT'
SELECT
  *
FROM
  `users`
WHERE
  EXISTS (
    SELECT
      *
    FROM
      `items`
    WHERE
      `users`.`id` = `items`.`user_id`
      AND `users`.`name` = 'test'
  )
  AND EXISTS (
    SELECT
      *
    FROM
      `items`
    WHERE
      `users`.`id` = `items`.`user_id`
      AND `users`.`name` = 'test'
  )
EOT;
        $this->assertSame(mb_strtolower($sql), $this->format($query));
    }
}
