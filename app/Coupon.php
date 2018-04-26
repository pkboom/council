<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    public static function normalize($code)
    {
        $coupon = static::where('code', $code)->first();

        return $coupon ?? new static;
    }

    public function against($plan)
    {
        if (!$this->worksWithPlan($plan)) {
            return false;
        }

        return $this->code;
    }

    public function worksWithPlan($plan)
    {
    }
}
