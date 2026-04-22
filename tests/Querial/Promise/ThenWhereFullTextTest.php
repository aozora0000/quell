<?php

declare(strict_types=1);

namespace Tests\Querial\Promise;

use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\Test;
use Querial\Promise\ThenWhereFullText;
use Tests\Querial\WithEloquentModelTestCase;

final class ThenWhereFullTextTest extends WithEloquentModelTestCase
{
    #[Test]
    public function リクエストにキーが存在する場合_full_text_searchクエリを発行する事を確認(): void
    {
        $request = Request::create('/', 'GET', ['name' => 'test']);

        $model = $this->createModel();
        $builder = $model->newQuery();

        $instance = new ThenWhereFullText('price');
        $sql = <<<'EOT'
select
  *
from
  `users`
where
  match (`price`) against ('' in natural language mode)
EOT;
        $this->assertSame(mb_strtolower($sql), $this->format($instance->resolve($request, $builder)));
    }
}
