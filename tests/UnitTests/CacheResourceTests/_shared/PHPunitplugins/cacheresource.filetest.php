<?php

declare(strict_types=1);

class Smarty_CacheResource_Filetest extends \Smarty\Cacheresource\File
{
    public $lock_time = 0;

    public function has_lock(\Smarty\Smarty $smarty, \Smarty\Template\Cached $cached)
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
