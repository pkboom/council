<?php

namespace App;

use Illuminate\Support\Facades\Redis;

class Visits
{
    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function reset()
    {
        Redis::del($this->cacheKey());
    }

    public function count()
    {
        return Redis::get($this->cacheKey()) ?? 0;
    }

    public function cacheKey()
    {
        return "threads.{$this->id}.visits";
    }

    public function record()
    {
        Redis::incr($this->cacheKey());
    }
}
