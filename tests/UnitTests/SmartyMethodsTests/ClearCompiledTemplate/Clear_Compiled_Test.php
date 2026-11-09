<?php

declare(strict_types=1);
/**
 * Smarty PHPunit tests for deleting compiled templates
 *

 * @author  Uwe Tews
 * @author  Rodney Rehm
 */

/**
 * class for delete compiled template tests
 *
 *
 *
 *
 */
class Clear_Compiled_Test extends PHPUnit_Smarty
{
    public function setUp(): void
    {
        $this->set_up_smarty(__DIR__);
        $this->smarty->add_template_dir('./templates_2/');
    }

    public function testInit()
    {
        $this->clean_dirs();
    }
    // helpers
    /**
     * clear $smarty->compile_dir
     *
     * @return void
     */
    protected function clear_files()
    {
        $this->clean_compile_dir();
    }

    /**
     * list of compiled files
     *
     * @var array
     */
    protected $_files = [];

    /**
     * generate compiled files
     *
     * @uses $_files to store references
     * @return array list of files array( id => path )
     */
    protected function make_files()
    {
        $this->_files = [];
        $directory_length = strlen($this->get_smarty()->get_compile_dir());
        $templates = [
            'helloworld.tpl'                => [null, 'compile1', 'compile2'],
            'helloworld2.tpl'               => [null, 'compile1', 'compile2'],
            'ambiguous/case1/foobar.tpl'    => [null, 'compile1', 'compile2'],
            '[1]ambiguous/case1/foobar.tpl' => [null, 'compile1', 'compile2'],
        ];

        foreach ($templates as $template => $compile_ids) {
            foreach ($compile_ids as $compile_id) {
                $tpl = $this->get_smarty()->create_template($template, null, $compile_id);
                $tpl->fetch();
                $this->_files[$template . '#' . $compile_id] = substr($tpl->get_compiled()->filepath, $directory_length);
            }
        }

        return $this->_files;
    }

    /**
     * Transform $id to $path
     *
     * @param  array $keys IDs like "template#compile_id"
     *
     * @return array list of (sorted) compiled file paths
     */
    protected function expect_files($keys)
    {
        $files = [];
        foreach ($keys as $key) {
            if (isset($this->_files[$key])) {
                $files[] = $this->_files[$key];
            }
        }
        sort($files);

        return $files;
    }

    /**
     * update mtime of compiled files
     *
     * @param  array  $keys   IDs like "template#compile_id"
     * @param  string $offset time offset added to time()
     *
     * @return void
     */
    protected function touch_files($keys, $offset = 0)
    {
        $base = $this->get_smarty()->get_compile_dir();
        $time = time();
        foreach ($keys as $key) {
            if (isset($this->_files[$key])) {
                file_put_contents($base . $this->_files[$key], ' #');
                touch($base . $this->_files[$key], $time + $offset);
            }
        }
        clearstatcache();
    }

    /**
     * find all compiled files
     *
     * @return array list of (sorted) compiled file paths
     */
    protected function get_files()
    {
        $directory = realpath($this->get_smarty()->get_compile_dir());
        if (!$directory) {
            return [];
        }

        $directory_length = strlen($directory);
        $files = [];

        $di = new RecursiveDirectoryIterator($directory);
        $it = new RecursiveIteratorIterator($di);
        foreach ($it as $file) {
            $_file = $file->__toString();
            // skip anything with a /. in it.
            if (preg_match("#[\\\\/]\.#", $_file) || substr((string)$file, -4) === '.txt' || !$file->isFile()) {
                continue;
            }

            $files[] = substr($file->__toString(), $directory_length + 1);
        }
        sort($files);

        return $files;
    }

    // Smarty::clear_compiled_template(null, null, null)
    public function testClearAll()
    {
        $this->run_clear_all(false);
    }

    public function testSubsClearAll()
    {
        $this->run_clear_all(true);
    }

    public function run_clear_all($use_sub_dirs)
    {
        $this->get_smarty()->set_use_sub_dirs($use_sub_dirs);
        $this->clear_files();
        $this->make_files();

        $expected = [];
        $this->assertEquals(12, $this->get_smarty()->clear_compiled_template());

        $this->assertEquals($this->expect_files($expected), $this->get_files());
        $this->clear_files();
    }

    // Smarty::clear_compiled_template($template, null, null)
    public function testClearTemplate()
    {
        $this->run_clear_template(false);
    }

    public function testSubsClearTemplate()
    {
        $this->run_clear_template(true);
    }

    public function testClearOtherTemplate()
    {
        $this->run_clear_other_template(false);
    }

    public function testSubsClearOtherTemplate()
    {
        $this->run_clear_other_template(true);
    }

    public function run_clear_template($use_sub_dirs)
    {
        $this->get_smarty()->set_use_sub_dirs($use_sub_dirs);
        $this->clear_files();
        $this->make_files();

        $expected = [
            'helloworld2.tpl#', 'helloworld2.tpl#compile1', 'helloworld2.tpl#compile2',
            'ambiguous/case1/foobar.tpl#', 'ambiguous/case1/foobar.tpl#compile1', 'ambiguous/case1/foobar.tpl#compile2',
            '[1]ambiguous/case1/foobar.tpl#', '[1]ambiguous/case1/foobar.tpl#compile1', '[1]ambiguous/case1/foobar.tpl#compile2',
        ];
        $this->assertEquals(3, $this->get_smarty()->clear_compiled_template('helloworld.tpl'));

        $this->assertEquals($this->expect_files($expected), $this->get_files());
        $this->clear_files();
    }

    public function run_clear_other_template($use_sub_dirs)
    {
        $this->get_smarty()->set_use_sub_dirs($use_sub_dirs);
        $this->clear_files();
        $this->make_files();

        $expected = array_keys($this->_files);
        $this->assertEquals(0, $this->get_smarty()->clear_compiled_template('foobar.tpl'));

        $this->assertEquals($this->expect_files($expected), $this->get_files());
        $this->clear_files();
    }

    // Smarty::clear_compiled_template(null, $cache_id, null)
    public function testClearCompileid()
    {
        $this->run_clear_compileid(false);
    }

    public function testSubsClearCompileid()
    {
        $this->run_clear_compileid(true);
    }

    public function testClearOtherCompileid()
    {
        $this->run_clear_other_compileid(false);
    }

    public function testSubsClearOtherCompileid()
    {
        $this->run_clear_other_compileid(true);
    }

    public function run_clear_compileid($use_sub_dirs)
    {
        $this->get_smarty()->set_use_sub_dirs($use_sub_dirs);
        $this->clear_files();
        $this->make_files();

        $expected = [
            'helloworld.tpl#', 'helloworld.tpl#compile2',
            'helloworld2.tpl#', 'helloworld2.tpl#compile2',
            'ambiguous/case1/foobar.tpl#', 'ambiguous/case1/foobar.tpl#compile2',
            '[1]ambiguous/case1/foobar.tpl#', '[1]ambiguous/case1/foobar.tpl#compile2',
        ];
        $count = $this->get_smarty()->clear_compiled_template(null, 'compile1');
        $this->assertEquals(4, $count);

        $this->assertEquals($this->expect_files($expected), $this->get_files());
        $this->clear_files();
    }

    public function run_clear_other_compileid($use_sub_dirs)
    {
        $this->get_smarty()->set_use_sub_dirs($use_sub_dirs);
        $this->clear_files();
        $this->make_files();

        $expected = array_keys($this->_files);
        $this->assertEquals(0, $this->get_smarty()->clear_compiled_template(null, 'other'));

        $this->assertEquals($this->expect_files($expected), $this->get_files());
        $this->clear_files();
    }

    // Smarty::clear_compiled_template(null, null, $expired)
    public function testClearExpired()
    {
        $this->run_clear_expired(false);
    }

    public function testSubsClearExpired()
    {
        $this->run_clear_expired(true);
    }

    public function run_clear_expired($use_sub_dirs)
    {
        $this->get_smarty()->set_use_sub_dirs($use_sub_dirs);
        $this->clear_files();
        $this->make_files();

        $expected = ['helloworld.tpl#', 'helloworld2.tpl#'];
        $this->touch_files(array_diff(array_keys($this->_files), $expected), - 1000);
        $this->assertEquals(10, $this->get_smarty()->clear_compiled_template(null, null, 500));

        $this->assertEquals($this->expect_files($expected), $this->get_files());
        $this->clear_files();
    }

    // Smarty::clear_compiled_template($template, null, $expired)
    public function testClearTemplateExpired()
    {
        $this->run_clear_template_expired(false);
    }

    public function testSubsClearTemplateExpired()
    {
        $this->run_clear_template_expired(true);
    }

    public function run_clear_template_expired($use_sub_dirs)
    {
        $this->get_smarty()->set_use_sub_dirs($use_sub_dirs);
        $this->clear_files();
        $this->make_files();

        $expected = [
            'helloworld.tpl#', 'helloworld.tpl#compile2',
            'helloworld2.tpl#', 'helloworld2.tpl#compile1', 'helloworld2.tpl#compile2',
            'ambiguous/case1/foobar.tpl#', 'ambiguous/case1/foobar.tpl#compile1', 'ambiguous/case1/foobar.tpl#compile2',
            '[1]ambiguous/case1/foobar.tpl#', '[1]ambiguous/case1/foobar.tpl#compile1', '[1]ambiguous/case1/foobar.tpl#compile2',
        ];
        $this->touch_files(['helloworld.tpl#compile1'], - 1000);
        $this->assertEquals(1, $this->get_smarty()->clear_compiled_template('helloworld.tpl', null, 500));

        $this->assertEquals($this->expect_files($expected), $this->get_files());
        $this->clear_files();
    }

    // Smarty::clear_compiled_template($template, $cache_id, $expired)
    public function testClearTemplateCacheidExpired()
    {
        $this->run_clear_template_cacheid_expired(false);
    }

    public function testSubsClearTemplateCacheidExpired()
    {
        $this->run_clear_template_cacheid_expired(true);
    }

    public function run_clear_template_cacheid_expired($use_sub_dirs)
    {
        $this->get_smarty()->set_use_sub_dirs($use_sub_dirs);
        $this->clear_files();
        $this->make_files();

        $expected = [
            'helloworld.tpl#', 'helloworld.tpl#compile2',
            'helloworld2.tpl#', 'helloworld2.tpl#compile1', 'helloworld2.tpl#compile2',
            'ambiguous/case1/foobar.tpl#', 'ambiguous/case1/foobar.tpl#compile1', 'ambiguous/case1/foobar.tpl#compile2',
            '[1]ambiguous/case1/foobar.tpl#', '[1]ambiguous/case1/foobar.tpl#compile1', '[1]ambiguous/case1/foobar.tpl#compile2',
        ];
        $this->touch_files(['helloworld.tpl#compile1', 'helloworld.tpl#compile2'], - 1000);
        $this->assertEquals(1, $this->get_smarty()->clear_compiled_template('helloworld.tpl', 'compile1', 500));

        $this->assertEquals($this->expect_files($expected), $this->get_files());
        $this->clear_files();
    }

    // Smarty::clear_compiled_template(null, $cache_id, $expired)
    public function testClearCacheidExpired()
    {
        $this->run_clear_cacheid_expired(false);
    }

    public function testSubsClearCacheidExpired()
    {
        $this->run_clear_cacheid_expired(true);
    }

    public function run_clear_cacheid_expired($use_sub_dirs)
    {
        $this->get_smarty()->set_use_sub_dirs($use_sub_dirs);
        $this->clear_files();
        $this->make_files();

        $expected = [
            'helloworld.tpl#', 'helloworld.tpl#compile2',
            'helloworld2.tpl#', 'helloworld2.tpl#compile1', 'helloworld2.tpl#compile2',
            'ambiguous/case1/foobar.tpl#', 'ambiguous/case1/foobar.tpl#compile1', 'ambiguous/case1/foobar.tpl#compile2',
            '[1]ambiguous/case1/foobar.tpl#', '[1]ambiguous/case1/foobar.tpl#compile1', '[1]ambiguous/case1/foobar.tpl#compile2',
        ];
        $this->touch_files(['helloworld.tpl#compile1'], - 1000);
        $this->assertEquals(1, $this->get_smarty()->clear_compiled_template(null, 'compile1', 500));

        $this->assertEquals($this->expect_files($expected), $this->get_files());
        $this->clear_files();
    }

    // Smarty::clear_compiled_template($template, $cache_id, null)
    public function testClearTemplateCacheid()
    {
        $this->run_clear_template_cacheid(false);
    }

    public function testSubsClearTemplateCacheid()
    {
        $this->run_clear_template_cacheid(true);
    }

    public function run_clear_template_cacheid($use_sub_dirs)
    {
        $this->get_smarty()->set_use_sub_dirs($use_sub_dirs);
        $this->clear_files();
        $this->make_files();

        $expected = [
            'helloworld.tpl#', 'helloworld.tpl#compile2',
            'helloworld2.tpl#', 'helloworld2.tpl#compile1', 'helloworld2.tpl#compile2',
            'ambiguous/case1/foobar.tpl#', 'ambiguous/case1/foobar.tpl#compile1', 'ambiguous/case1/foobar.tpl#compile2',
            '[1]ambiguous/case1/foobar.tpl#', '[1]ambiguous/case1/foobar.tpl#compile1', '[1]ambiguous/case1/foobar.tpl#compile2',
        ];
        $this->assertEquals(1, $this->get_smarty()->clear_compiled_template('helloworld.tpl', 'compile1'));

        $this->assertEquals($this->expect_files($expected), $this->get_files());
        $this->clear_files();
    }

    public function testClearAmbiguousTemplate()
    {
        $this->run_clear_ambiguous_template(false);
    }

    public function testSubsAmbiguousTemplate()
    {
        $this->run_clear_ambiguous_template(true);
    }

    public function run_clear_ambiguous_template($use_sub_dirs)
    {
        $this->get_smarty()->set_use_sub_dirs($use_sub_dirs);
        $this->clear_files();
        $this->make_files();

        // TODO: uwe.tews - shouldn't clear_compiled_template("foo.tpl") remove "{$template_dir[0]}/foo.tpl" AND "{$template_dir[1]}/foo.tpl"?
        // currently it kills only the first one found (through regular template file identification methods)

        $expected = [
            'helloworld.tpl#', 'helloworld.tpl#compile1', 'helloworld.tpl#compile2',
            'helloworld2.tpl#', 'helloworld2.tpl#compile1', 'helloworld2.tpl#compile2',
            '[1]ambiguous/case1/foobar.tpl#', '[1]ambiguous/case1/foobar.tpl#compile1', '[1]ambiguous/case1/foobar.tpl#compile2',
        ];
        $this->assertEquals(3, $this->get_smarty()->clear_compiled_template('ambiguous/case1/foobar.tpl'));

        $this->assertEquals($this->expect_files($expected), $this->get_files());
        $this->clear_files();
    }
}
