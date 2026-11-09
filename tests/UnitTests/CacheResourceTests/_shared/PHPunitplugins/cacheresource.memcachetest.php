<?php

declare(strict_types=1);

use Smarty\Template;
use Smarty\Template\Cached;

require_once __DIR__ . '/../../../__shared/cacheresources/cacheresource.memcache.php';

class Smarty_CacheResource_Memcachetest extends Smarty_CacheResource_Memcache
{
    public $lockTime = 0;

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

    public function get(Template $_template)
    {
        $this->contents = [];
        $this->timestamps = [];
        $t = $this->get_content($_template);

        return $t ? $t : null;
    }

}
