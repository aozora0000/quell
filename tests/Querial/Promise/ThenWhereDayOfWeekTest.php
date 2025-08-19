<?php

namespace Tests\Querial\Promise;

use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\Test;
use Querial\Promise\ThenWhereDayOfWeek;
use Tests\Querial\WithEloquentModelTestCase;

class ThenWhereDayOfWeekTest extends WithEloquentModelTestCase
{
    #[Test]
    public function 曜日での絞り込みが適用される(): void
    {
        // dow=1 → DAYOFWEEK(created_at) = 1
        $request = Request::create('/', 'GET', ['dow' => '1']);
        $model = $this->createModel();
        $builder = $model->newQuery();

        $instance = new ThenWhereDayOfWeek('created_at', 'dow');

        $expected = <<<'EOT'
SELECT
  *
FROM
  `users`
WHERE
  DAYOFWEEK(`users`.`created_at`) = 1
EOT;
        $this->assertSame(mb_strtolower($expected), $this->format($instance->resolve($request, $builder)), 'DAYOFWEEKのクエリが一致しません');
    }
}
