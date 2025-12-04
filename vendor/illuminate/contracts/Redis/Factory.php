<?php

namespace Lines202512\Illuminate\Contracts\Redis;

interface Factory
{
    /**
     * Get a Redis connection by name.
     *
     * @param  \UnitEnum|string|null  $name
     * @return \Illuminate\Redis\Connections\Connection
     */
    public function connection($name = null);
}
