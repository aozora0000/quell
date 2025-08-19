<?php

namespace Tests\Querial\Promise;

use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\Test;
use Querial\Promise\ThenWhereRegexNot;
use Tests\Querial\WithEloquentModelTestCase;

class ThenWhereRegexNotTest extends WithEloquentModelTestCase
{
    #[Test]
    public function 正規表現の否定検索が適用される(): void
    {
        // name に対して NOT REGEXP ^t.*$ を適用
        $request = Request::create('/', 'GET', ['name_regex_not' => '^t.*$']);
        $model = $this->createModel();
        $builder = $model->newQuery();

        $instance = new ThenWhereRegexNot('name', 'name_regex_not');

        $expected = <<<'EOT'
SELECT
  *
FROM
  `users`
WHERE
  `users`.`name` NOT REGEXP '^t.*$'
EOT;
        $this->assertSame(mb_strtolower($expected), $this->format($instance->resolve($request, $builder)), 'NOT REGEXPのクエリが一致しません');
    }
}
