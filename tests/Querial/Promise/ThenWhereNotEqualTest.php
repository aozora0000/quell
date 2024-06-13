<?php

namespace Tests\Querial\Promise;

use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Querial\Promise\ThenWhereNotEqual;
use Tests\Querial\WithEloquentModelTestCase;

/**
 * @property Builder $builder
 */
class ThenWhereNotEqualTest extends WithEloquentModelTestCase
{
    /**
     * @test
     */
    public function リクエストに存在するキーでwhereを掛ける(): void
    {
        $request = Request::create('/', 'GET', ['name' => 'test', 'email' => 'email@email.com']);
        $model = $this->createModel();
        $query = $model->newQuery();

        // リクエストに存在するキーでwhereを掛ける
        $query = (new ThenWhereNotEqual('name'))->resolve($request, $query);
        $sql = <<<'EOT'
SELECT
  *
FROM
  "users"
WHERE
  "users"."name" <> 'test'
EOT;
        $this->assertSame($sql, $this->format($query));
    }

    /**
     * @test
     */
    public function リクエストに存在するキーでandwhereを掛ける(): void
    {
        $request = Request::create('/', 'GET', ['name' => 'test', 'email' => 'email@email.com']);
        $model = $this->createModel();
        $query = $model->newQuery();

        // リクエストに存在するキーでand whereを掛ける
        $query = (new ThenWhereNotEqual('name'))->resolve($request, $query);
        $query = (new ThenWhereNotEqual('email'))->resolve($request, $query);
        $sql = <<<'EOT'
SELECT
  *
FROM
  "users"
WHERE
  "users"."name" <> 'test'
  AND "users"."email" <> 'email@email.com'
EOT;
        $this->assertSame($sql, $this->format($query));
    }

    /**
     * @test
     */
    public function リクエストに存在しないキーの場合、SQLには反映されない(): void
    {

        $request = Request::create('/', 'GET', ['name' => 'test', 'email' => 'email@email.com']);
        $model = $this->createModel();
        $query = $model->newQuery();

        // リクエストに存在しないキーの場合、SQLには反映されない
        $query = (new ThenWhereNotEqual('name'))->resolve($request, $query);
        $query = (new ThenWhereNotEqual('noattr'))->resolve($request, $query);
        $sql = <<<'EOT'
SELECT
  *
FROM
  "users"
WHERE
  "users"."name" <> 'test'
EOT;
        $this->assertSame($sql, $this->format($query));
    }

    /**
     * @test
     */
    public function 検索するテーブルを指定してクエリを作成する(): void
    {
        $request = Request::create('/', 'GET', ['name' => 'test', 'email' => 'email@email.com']);
        $model = $this->createModel();
        $query = $model->newQuery();

        // 検索するテーブルを指定してクエリを作成する
        $query = (new ThenWhereNotEqual('name', null, 'items'))->resolve($request, $query);
        $sql = <<<'EOT'
SELECT
  *
FROM
  "users"
WHERE
  "items"."name" <> 'test'
EOT;
        $this->assertSame($sql, $this->format($query));
    }
}
