<?php

namespace App;


use Illuminate\Support\Facades\Redis;

trait RecordsVisits
{

    public function resetVisits()
    {
        Redis::del($this->visitsCacheKey());

        return $this;
    }

    public function recordVisits()
    {
        Redis::incr($this->visitsCacheKey());

        return $this;
    }

    public function visits()
    {
        return Redis::get($this->visitsCacheKey()) ?? 0;
    }

    protected function visitsCacheKey()
    {
        return "threads.{$this->id}.visits";
    }
}
