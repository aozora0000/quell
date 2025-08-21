<?php

namespace Tests\Querial\Promise;

use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\Test;
use Querial\Promise\ThenWhereStartsWith;
use Tests\Querial\WithEloquentModelTestCase;

class ThenWhereStartsWithTest extends WithEloquentModelTestCase
{
    #[Test]
    public function 指定文字列で前方一致の_like検索が行われる(): void
    {
        $request = Request::create('/', 'GET', ['name' => 'test']);
        $model = $this->createModel();
        $builder = $model->newQuery();

        $instance = new ThenWhereStartsWith('name');

        $expected = <<<'EOT'
SELECT
  *
FROM
  `users`
WHERE
  `users`.`name` LIKE 'test%'
EOT;
        $actual = $this->format($instance->resolve($request, $builder));
        $this->assertSame(mb_strtolower($expected), $actual, '前方一致のLIKEクエリが一致しません');
    }
}
