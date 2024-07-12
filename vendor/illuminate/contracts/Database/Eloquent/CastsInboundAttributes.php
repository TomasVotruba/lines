<?php

namespace Lines202407\Illuminate\Contracts\Database\Eloquent;

use Lines202407\Illuminate\Database\Eloquent\Model;
interface CastsInboundAttributes
{
    /**
     * Transform the attribute to its underlying model values.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return mixed
     */
    public function set(Model $model, string $key, $value, array $attributes);
}
