<?php

namespace Tests\Querial\Promise;

use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\Test;
use Querial\Promise\ThenWhereDistance;
use Tests\Querial\WithEloquentModelTestCase;

class ThenWhereDistanceTest extends WithEloquentModelTestCase
{
    #[Test]
    public function 半径内検索のwhere_rawが付与される(): void
    {
        // lat/lng=35,139 半径10km
        $request = Request::create('/', 'GET', ['lat' => '35', 'lng' => '139', 'radius' => '10']);
        $model = $this->createModel();
        $builder = $model->newQuery();

        $instance = new ThenWhereDistance('lat', 'lng');

        $sql = $this->format($instance->resolve($request, $builder));

        // 文字列包含を1アサーションで確認（SQL方言差異を吸収）
        $this->assertTrue(
            str_contains($sql, 'where') &&
            str_contains($sql, 'acos(') &&
            str_contains($sql, ' <= ') &&
            (str_contains($sql, "'10'") || str_contains($sql, ' 10')),
            '距離検索のWHERE句が期待通りに含まれていません'
        );
    }
}
