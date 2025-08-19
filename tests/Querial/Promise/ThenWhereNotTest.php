<?php

namespace Tests\Querial\Promise;

use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\Test;
use Querial\Promise\ThenWhereEqual;
use Querial\Promise\ThenWhereNot;
use Tests\Querial\WithEloquentModelTestCase;

class ThenWhereNotTest extends WithEloquentModelTestCase
{
    #[Test]
    public function 否定条件が適用される(): void
    {
        // name = 'test' を否定して NOT (name = 'test') を生成
        $request = Request::create('/', 'GET', ['name' => 'test']);
        $model = $this->createModel();
        $builder = $model->newQuery();

        $instance = new ThenWhereNot(new ThenWhereEqual('name'));

        $sql = $this->format($instance->resolve($request, $builder));

        // 文字列包含の単一アサーション
        $this->assertTrue(
            str_contains($sql, 'where') &&
            str_contains($sql, 'not') &&
            str_contains($sql, "`users`.`name` = 'test'"),
            'NOTで包まれた条件が期待通りに含まれていません'
        );
    }
}
