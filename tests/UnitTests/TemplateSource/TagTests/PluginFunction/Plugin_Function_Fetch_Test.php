<?php

declare(strict_types=1);

/**
 * class testing fetch function
 */
class Plugin_Function_Fetch_Test extends PHPUnit_Smarty
{
    public function setUp(): void
    {
        $this->set_up_smarty(__DIR__);
    }

    public function testInit()
    {
        $this->clean_dirs();
    }

    /**
     * test {fetch} from local file
     */
    public function testFetchFile()
    {
        $this->assertStringContainsString(
            'ct4hn8nzgm;cgzm;',
            $this->smarty->fetch('string:{fetch file="./testfile.txt"}')
        );
    }

    /**
     * test {fetch} non-existing file
     */
    public function testFetchNonExistingFile()
    {
        $this->expectException(\Smarty\Exception::class);
        $this->expectExceptionMessage('{fetch} cannot read resource \'./no/such/file\'');
        $this->smarty->fetch('string:{fetch file="./no/such/file"}');
    }

    /**
     * test {fetch file=...} access to file from path not aloo/wed by security settings
     *
     * @run InSeparateProcess
     *
     */
    public function testFetchSecurity()
    {
        $this->expectException(\Smarty\Exception::class);
        $this->expectExceptionMessage('not trusted file path');
        $this->clean_dirs();
        $dir = $this->smarty->get_template_dir();
        $this->smarty->enable_security();
        $this->smarty->fetch('string:{fetch file=\''. $dir[0]. '../../../../../etc/passwd\'}');
    }
    /**
     * test {fetch file=...} access to file from path not aloo/wed by security settings
     *
     * @run InSeparateProcess
     *
     */
    public function testFetchSecurity2()
    {
        $this->expectException(\Smarty\Exception::class);
        $this->expectExceptionMessage('not trusted file path');
        $this->clean_dirs();
        $this->smarty->get_template_dir();
        $this->smarty->enable_security();
        $this->smarty->set_template_dir('/templates');
        $this->smarty->fetch('string:{fetch file="/templates/../etc/passwd"}');
    }

}
