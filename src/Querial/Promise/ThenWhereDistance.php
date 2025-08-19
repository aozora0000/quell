<?php

declare(strict_types=1);

namespace Querial\Promise;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Http\Request;
use Querial\Contracts\Support\PromiseQuery;
use Querial\Target\ScalarTarget;

/**
 * 緯度経度と半径から、Haversine で距離検索を行う Promise。
 *
 * デフォルト単位は km。地球半径は 6371km。
 */
class ThenWhereDistance extends PromiseQuery
{
    protected ScalarTarget $latTarget;
    protected ScalarTarget $lngTarget;
    protected ScalarTarget $radiusTarget;

    /**
     * @param string $latitudeColumn テーブル上の緯度カラム名
     * @param string $longitudeColumn テーブル上の経度カラム名
     * @param string $latKey リクエストの緯度キー
     * @param string $lngKey リクエストの経度キー
     * @param string $radiusKey リクエストの半径キー
     * @param float $earthRadiusKm 地球半径(km)
     */
    public function __construct(
        protected string $latitudeColumn,
        protected string $longitudeColumn,
        protected string $latKey = 'lat',
        protected string $lngKey = 'lng',
        protected string $radiusKey = 'radius',
        protected float $earthRadiusKm = 6371.0,
    ) {
        $this->latTarget = new ScalarTarget($this->latKey);
        $this->lngTarget = new ScalarTarget($this->lngKey);
        $this->radiusTarget = new ScalarTarget($this->radiusKey);
    }

    public function resolve(Request $request, EloquentBuilder $builder): EloquentBuilder
    {
        if (! $this->match($request)) {
            return $builder;
        }

        $table = $builder->getModel()->getTable();
        $latCol = sprintf('`%s`.`%s`', $table, $this->latitudeColumn);
        $lngCol = sprintf('`%s`.`%s`', $table, $this->longitudeColumn);

        $lat = (float) $request->str($this->latKey, '0')->value();
        $lng = (float) $request->str($this->lngKey, '0')->value();
        $radius = (float) $request->str($this->radiusKey, '0')->value();

        // Haversine 公式（MySQL）
        $expr = sprintf(
            '(%f * acos(cos(radians(%s)) * cos(radians(%s)) * cos(radians(%s) - radians(%s)) + sin(radians(%s)) * sin(radians(%s))))',
            $this->earthRadiusKm,
            $this->quote($lat),
            $latCol,
            $lngCol,
            $this->quote($lng),
            $this->quote($lat),
            $latCol,
        );

        return $builder->whereRaw($expr.' <= '.$this->quote($radius));
    }

    public function match(Request $request): bool
    {
        if (! $this->latTarget->is($request)) {
            return false;
        }
        if (! $this->lngTarget->is($request)) {
            return false;
        }
        if (! $this->radiusTarget->is($request)) {
            return false;
        }
        return true;
    }

    /**
     * 数値をSQL文字列としてクォートする（toRawSqlとの比較安定化のため単純なシングルクォート）
     */
    private function quote(float $number): string
    {
        // 小数点以下はそのまま出力（Formatter側で正規化される）
        return "'".rtrim(rtrim(sprintf('%.6F', $number), '0'), '.')."'";
    }
}
