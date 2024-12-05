<?php

namespace Lines202412\Illuminate\Container\Attributes;

use Attribute;
use Lines202412\Illuminate\Contracts\Container\Container;
use Lines202412\Illuminate\Contracts\Container\ContextualAttribute;
#[Attribute(Attribute::TARGET_PARAMETER)]
class Database implements ContextualAttribute
{
    /**
     * @var string|null
     */
    public $connection;
    /**
     * Create a new class instance.
     */
    public function __construct(?string $connection = null)
    {
        $this->connection = $connection;
    }
    /**
     * Resolve the database connection.
     *
     * @param  self  $attribute
     * @param  \Illuminate\Contracts\Container\Container  $container
     * @return \Illuminate\Database\Connection
     */
    public static function resolve(self $attribute, Container $container)
    {
        return $container->make('db')->connection($attribute->connection);
    }
}
