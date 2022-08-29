<?php

namespace App\Repositories\Eloquent\Criteria;

use Illuminate\Support\Carbon;
use App\Repositories\Criteria\ICriterion;

class IsLive implements ICriterion
{
    public function apply($model)
    {
        return $model
            ->where('publication_date', '<=', Carbon::now())
            // ->where('expire', '>', Carbon::now())
            // ->orWhereNull('expire')
            ->where(function ($query) {
                return $query->where('expire_at', '>=', Carbon::now())
                    ->orWhereNull('expire_at');;
            });
    }
}
