# LaravelQuerial
[![Laravel 10](https://img.shields.io/badge/Laravel-10-orange.svg)](http://laravel.com)

PromiseLike Chains IlluminateRequest to QueryBuilder

## How To Use

### use Quell
```SearchQuell.php
class SearchQuell extends Quell
{
    /**
     * When (promises,failed,finally) Not Works.
     * @param Builder $builder
     * @return Builder|null
     */
    protected function default(Builder $builder): Builder
    {
        return $builder->limit(10);
    }

    /**
     * When Throwable throw
     * @return callable|null
     */
    protected function failed(): ?callable
    {
        return null;
    }

    /**
     * try~catch~finally
     * @return callable|null
     */
    protected function finally(): ?callable
    {
        return null;
    }

    protected function promise(): ?PromiseInterface
    {
        return new ThenWherePromisesAggregator([
            new ThenWhereEqualWithQuery('name'),
            new ThenWhereBetweenWithQuery('created_at'),
        ]);
    }
}
```
```controller.php
public function searchIndex(SearchQuell $query)
{
    // ?name={name}&created_at_min={Y-m-d}&created_at_max={Y-m-d}
    /** 
     * where (
     *     "users"."name" = '{request.name}' and 
     *     "users"."created_at" 
     *          between '{request.created_at_min}' and '{request.created_at_max}'
     * )
     */
    $items = $query->build(User::query())->get();
    return response()->json($items);
}
```

### use Pipeline
```controller.php
public function searchIndex(\Illuminate\Http\Request $request)
{
    $pipeline = new Pipeline($request);
    $pipeline
         // where name = `{request.name}` AND
        ->then(new ThenWhereEqualWithQuery('name'))
        
         // where comment_id IN (`{request.comment_id}`) AND
        ->then(new ThenWhereInArrayWithQuery('comment_id'))
        
        // where exists (
        //    select * from users where users.id = posts.id AND
        //    (
        //       where users.name LIKE '%{request.user.name}%' OR
        //       where users.email LIKE '%{request.user.email}%'
        //    )
        // )
        ->then(new ThenWhereHasRelation('user', new ThenWherePromisesAggregator([
            new ThenOrWhereLikeWithQuery('name', 'user.name'),
            new ThenOrWhereLikeWithQuery('email', 'user.email'),
        ])))
        ->build(Post::with(['user']));
}
```