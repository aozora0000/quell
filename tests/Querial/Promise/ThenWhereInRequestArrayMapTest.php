<?php

namespace Tests\Querial\Promise;

use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\Test;
use Querial\Promise\ThenWhereInRequestArrayMap;
use Tests\Querial\WithEloquentModelTestCase;

class ThenWhereInRequestArrayMapTest extends WithEloquentModelTestCase
{
    #[Test]
    public function 連想配列を_mapに従って_andで_where_in適用できる(): void
    {
        // filters[status][]=active&filters[status][]=inactive&filters[role][]=admin&filters[role][]=user
        $request = Request::create('/', 'GET', [
            'filters' => [
                'status' => ['active', 'inactive'],
                'role' => ['admin', 'user'],
            ],
        ]);
        $model = $this->createModel();
        $builder = $model->newQuery();

        // 入力キー→カラム名のマップ
        $instance = new ThenWhereInRequestArrayMap([
            'status' => 'status',
            'role' => 'role',
        ], 'filters');

        $expected = <<<'EOT'
SELECT
  *
FROM
  `users`
WHERE
  `users`.`status` IN ('active', 'inactive')
  AND `users`.`role` IN ('admin', 'user')
EOT;
        $actual = $this->format($instance->resolve($request, $builder));
        $this->assertSame(mb_strtolower($expected), $actual, '連想配列マップでの複数 where in が期待と一致しません');
    }

    #[Test]
    public function スカラ値も単一要素配列として_where_in適用できる(): void
    {
        // filters[status]=active（配列でなくても IN('active') として適用）
        $request = Request::create('/', 'GET', [
            'filters' => [
                'status' => 'active',
            ],
        ]);
        $model = $this->createModel();
        $builder = $model->newQuery();

        $instance = new ThenWhereInRequestArrayMap([
            'status' => 'status',
        ], 'filters');

        $expected = <<<'EOT'
SELECT
  *
FROM
  `users`
WHERE
  `users`.`status` IN ('active')
EOT;
        $actual = $this->format($instance->resolve($request, $builder));
        $this->assertSame(mb_strtolower($expected), $actual, 'スカラ入力の where in が期待と一致しません');
    }
}
