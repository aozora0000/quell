<?php

declare(strict_types=1);

namespace Querial\Promise;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Http\Request;
use Querial\Contracts\Support\PromiseQuery;
use Querial\Target\ScalarTarget;

/**
 * limit/offset、あるいはper_page/pageパラメータから LIMIT / OFFSET を適用するPromise。
 *
 * - limit/offset が優先。どちらか一方のみの場合は存在する方だけ適用。
 * - limit/offset が無ければ per_page, page から算出（pageは1始まり、負値や0は1に矯正）。
 */
class ThenLimitOffset extends PromiseQuery
{
    protected ScalarTarget $limitTarget;

    protected ScalarTarget $offsetTarget;

    protected ScalarTarget $perPageTarget;

    protected ScalarTarget $pageTarget;

    public function __construct(
        protected string $limitKey = 'limit',
        protected string $offsetKey = 'offset',
        protected string $perPageKey = 'per_page',
        protected string $pageKey = 'page',
    ) {
        $this->limitTarget = new ScalarTarget($this->limitKey);
        $this->offsetTarget = new ScalarTarget($this->offsetKey);
        $this->perPageTarget = new ScalarTarget($this->perPageKey);
        $this->pageTarget = new ScalarTarget($this->pageKey);
    }

    public function resolve(Request $request, EloquentBuilder $builder): EloquentBuilder
    {
        if ($this->limitTarget->is($request)) {
            $limit = (int) $request->str($this->limitKey, '0')->value();
            if ($limit > 0) {
                $builder->limit($limit);
            }
            if ($this->offsetTarget->is($request)) {
                $offset = (int) $request->str($this->offsetKey, '0')->value();
                if ($offset > 0) {
                    $builder->offset($offset);
                }
            }

            return $builder;
        }

        if ($this->perPageTarget->is($request)) {
            $perPage = max(1, (int) $request->str($this->perPageKey, '0')->value());
            $page = 1;
            if ($this->pageTarget->is($request)) {
                $page = max(1, (int) $request->str($this->pageKey, '1')->value());
            }
            $offset = ($page - 1) * $perPage;

            $builder->limit($perPage);
            if ($offset > 0) {
                $builder->offset($offset);
            }
        }

        return $builder;
    }

    public function match(Request $request): bool
    {
        return $this->limitTarget->is($request) || $this->perPageTarget->is($request);
    }
}
