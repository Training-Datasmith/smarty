<?php

declare(strict_types=1);

use Smarty\Template\Cached;

require_once __DIR__ . '/../../__shared/cacheresources/cacheresource.pdo_gzip.php';

class Smarty_CacheResource_Pdo_Gziptest extends Smarty_CacheResource_Pdo_Gzip
{
    public $lock_time = 0;

    public function has_lock(\Smarty\Smarty $smarty, Cached $cached)
    {
        if ($this->lock_time) {
            $this->lock_time--;
            if (!$this->lock_time) {
                $this->release_lock($smarty, $cached);
            }
        }
        return parent::has_lock($smarty, $cached);
    }
}
