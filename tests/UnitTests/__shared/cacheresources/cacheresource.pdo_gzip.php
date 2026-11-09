<?php

declare(strict_types=1);

require_once __DIR__ . '/cacheresource.pdo.php';

/**
 * PDO Cache Handler with GZIP support
 * Example usage :
 *      $cnx    =   new PDO("mysql:host=localhost;dbname=mydb", "username", "password");
 *      $smarty->set_caching_type('pdo_gzip');
 *      $smarty->register_cache_resource('pdo_gzip', new Smarty_CacheResource_Pdo_Gzip($cnx, 'smarty_cache'));
 *
 * @require Smarty_CacheResource_Pdo class
 * @author  Beno!t POLASZEK - 2014
 */
class Smarty_CacheResource_Pdo_Gzip extends Smarty_CacheResource_Pdo
{
    /**
     * Encodes the content before saving to database
     *
     * @param string $content
     *
     * @return string $content
     * @access protected
     */
    protected function input_content($content)
    {
        return gzdeflate($content);
    }

    /**
     * Decodes the content before saving to database
     *
     * @param string $content
     *
     * @return string $content
     * @access protected
     */
    protected function output_content($content)
    {
        return gzinflate($content);
    }
}
