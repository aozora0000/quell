<?php

namespace Tests\Querial\Promise;

use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Querial\Promise\ThenWhereEqual;
use Tests\Querial\WithEloquentModelTestCase;

/**
 * @property Builder $builder
 */
class ThenWhereEqualTest extends WithEloquentModelTestCase
{
    /**
     * @test
     */
    public function リクエストにキーが存在する場合Whereクエリを発行する事を確認(): void
    {
        $request = Request::create('/', 'GET', ['name' => 'test', 'email' => 'email@email.com']);
        $model = $this->createModel();
        $query = $model->newQuery();

        // リクエストに存在するキーでwhereを掛ける
        $query = (new ThenWhereEqual('name'))->resolve($request, $query);
        $sql = <<<'EOT'
SELECT
  *
FROM
  "users"
WHERE
  "users"."name" = 'test'
EOT;
        $this->assertSame($sql, $this->format($query));
    }

    /**
     * @test
     */
    public function 別テーブルでもWhereイコールが出来る事を確認(): void
    {
        $request = Request::create('/', 'GET', ['name' => 'test', 'email' => 'email@email.com']);
        $model = $this->createModel();
        $query = $model->newQuery();

        // 検索するテーブルを指定してクエリを作成する
        $query = (new ThenWhereEqual('name', null, 'items'))->resolve($request, $query);
        $sql = <<<'EOT'
SELECT
  *
FROM
  "users"
WHERE
  "items"."name" = 'test'
EOT;
        $this->assertSame($sql, $this->format($query));
    }
}
