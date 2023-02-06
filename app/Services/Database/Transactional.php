<?php

declare(strict_types = 1);

namespace App\Services\Database;

use Closure;
use Illuminate\Database\ConnectionInterface;

class Transactional
{
    public function __construct(
        private readonly ConnectionInterface $db
    ) { }

    /**
     * @param \Closure $callback
     * @param int      $attempts
     *
     * @return mixed
     * @throws \Throwable
     */
    public function wrap(Closure $callback, int $attempts = 1): mixed
    {
        return $this->db->transaction(...func_get_args());
    }
}
