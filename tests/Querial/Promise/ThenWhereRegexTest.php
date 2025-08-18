<?php

namespace Tests\Querial\Promise;

use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\Test;
use Querial\Promise\ThenWhereRegex;
use Tests\Querial\WithEloquentModelTestCase;

class ThenWhereRegexTest extends WithEloquentModelTestCase
{
    #[Test]
    public function 正規表現検索が適用される(): void
    {
        // name に対して正規表現 ^t.*$ を適用
        $request = Request::create('/', 'GET', ['name_regex' => '^t.*$']);
        $model = $this->createModel();
        $builder = $model->newQuery();

        $instance = new ThenWhereRegex('name', 'name_regex');

        $expected = <<<'EOT'
SELECT
  *
FROM
  `users`
WHERE
  `users`.`name` REGEXP '^t.*$'
EOT;
        $this->assertSame(mb_strtolower($expected), $this->format($instance->resolve($request, $builder)), 'REGEXPのクエリが一致しません');
    }
}
