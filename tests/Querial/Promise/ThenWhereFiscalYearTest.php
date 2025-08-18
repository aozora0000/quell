<?php

namespace Tests\Querial\Promise;

use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\Test;
use Querial\Promise\ThenWhereFiscalYear;
use Tests\Querial\WithEloquentModelTestCase;

class ThenWhereFiscalYearTest extends WithEloquentModelTestCase
{
    #[Test]
    public function 既定の4月始まり年度で期間絞り込みを行う(): void
    {
        // 2024年度（4/1〜翌年3/31 23:59:59）
        $request = Request::create('/', 'GET', ['year' => '2024']);
        $model = $this->createModel();
        $builder = $model->newQuery();

        $instance = new ThenWhereFiscalYear('created_at', 4, 'year');

        $expected = <<<'EOT'
SELECT
  *
FROM
  `users`
WHERE
  `users`.`created_at` BETWEEN '2024-04-01 00:00:00'
  AND '2025-03-31 23:59:59'
EOT;
        $this->assertSame(mb_strtolower($expected), $this->format($instance->resolve($request, $builder)), '4月始まり年度のbetweenクエリが一致しません');
    }
}
