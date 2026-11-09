<?php

declare(strict_types=1);

use Smarty\Template\Cached;

require_once __DIR__ . '/../../../__shared/cacheresources/cacheresource.mysql.php';

class Smarty_CacheResource_Mysqltest extends Smarty_CacheResource_Mysql
{
    public $lockTime = 0;

    protected function db(): PDO
    {
        return PHPUnit_Smarty::$pdo;
    }

    public function has_lock(\Smarty\Smarty $smarty, Cached $cached)
    {
        if ($this->lockTime) {
            $this->lockTime--;
            if (!$this->lockTime) {
                $this->release_lock($smarty, $cached);
            }
        }
        return parent::has_lock($smarty, $cached);
    }
}
