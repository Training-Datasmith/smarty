<?php

declare(strict_types=1);
/**
 * Smarty PHPunit tests for File resources
 *

 * @author  Uwe Tews
 */

use Smarty\Exception;

/**
 * class for file resource tests
 *
 *
 *
 *
 */
class File_Resource_Test extends PHPUnit_Smarty
{
    public function setUp(): void
    {
        $this->set_up_smarty(__DIR__);
        $this->smarty->enable_security();
    }

    public function testInit()
    {
        $this->clean_dirs();
    }

    protected function relative($path)
    {
        $path = str_replace(str_replace('\\', '/', __DIR__), '.', str_replace('\\', '/', $path));

        return $path;
    }

    /**
     *
     */
    public function testGetTemplateResourceName()
    {
        $tpl = $this->smarty->create_template('helloworld.tpl');
        $this->assertEquals('helloworld.tpl', $tpl->get_source()->get_resource_name());
    }

    public function testTemplateFileExists1()
    {
        $tpl = $this->smarty->create_template('helloworld.tpl');
        $this->assertTrue($tpl->get_source()->exists);
    }

    public function testTemplateFileExists2()
    {
        $this->assertTrue($this->smarty->template_exists('helloworld.tpl'));
    }

    public function testTemplateFileNotExists1()
    {
        $tpl = $this->smarty->create_template('notthere.tpl');
        $this->assertFalse($tpl->get_source()->exists);
    }

    public function testTemplateFileNotExists2()
    {
        $this->assertFalse($this->smarty->template_exists('notthere.tpl'));
    }

    /**
     * test not existing file
     */
    public function testTemplateFileNotExists3()
    {
        $this->expectException(\Smarty\Exception::class);
        $this->expectExceptionMessage('Unable to');
        $this->expectExceptionMessage('notthere.tpl');
        $this->smarty->fetch('notthere.tpl');
    }

    public function testGetTemplateTimestamp()
    {
        $tpl = $this->smarty->create_template('helloworld.tpl');
        $this->assertTrue(is_integer($tpl->get_source()->get_time_stamp()));
        $this->assertEquals(10, strlen((string) $tpl->get_source()->get_time_stamp()));
    }

    public function testGetTemplateSource()
    {
        $tpl = $this->smarty->create_template('helloworld.tpl');
        $this->assertEquals('hello world', $tpl->get_source()->get_content());
    }

    public function testUsesCompiler()
    {
        $tpl = $this->smarty->create_template('helloworld.tpl');
        $this->assertTrue($tpl->get_source()->handler->supports_compiled_templates());
        $this->assertNotEmpty($tpl->get_compiled()->filepath);
    }

    public function testIsEvaluated()
    {
        $tpl = $this->smarty->create_template('helloworld.tpl');
        $this->assertFalse($tpl->get_source()->handler->recompiled);
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testGetCompiledTimestampPrepare()
    {
        $tpl = $this->smarty->create_template('helloworld.tpl');
        // create dummy compiled file
        file_put_contents($tpl->get_compiled()->filepath, '<?php ?>');
        touch($tpl->get_compiled()->filepath, $tpl->get_source()->get_time_stamp());
    }

    /**
     *
     */
    public function testGetCompiledTimestamp()
    {
        $tpl = $this->smarty->create_template('helloworld.tpl');
        $this->assertTrue(is_integer($tpl->get_compiled()->get_time_stamp()));
        $this->assertEquals(10, strlen((string) $tpl->get_compiled()->get_time_stamp()));
        $this->assertEquals($tpl->get_compiled()->get_time_stamp(), $tpl->get_source()->get_time_stamp());
    }

    public function testMustCompileExisting()
    {
        $tpl = $this->smarty->create_template('helloworld.tpl');
        $this->assertFalse($tpl->must_compile());
    }

    public function testMustCompileAtForceCompile()
    {
        $this->smarty->set_force_compile(true);
        $tpl = $this->smarty->create_template('helloworld.tpl');
        $this->assertTrue($tpl->must_compile());
    }

    /**
     * @group slow
     */
    public function testMustCompileTouchedSource()
    {
        // touch to prepare next test
        sleep(2);
        $this->smarty->create_template('helloworld.tpl');
        touch(__DIR__ . '/templates/helloworld.tpl');

        $this->setUp();

        $tpl = $this->smarty->create_template('helloworld.tpl');
        $this->assertTrue($tpl->must_compile());
        // clean up for next tests
        $this->clean_dir($this->smarty->get_compile_dir());
    }

    public function testCompileTemplateFile()
    {
        $tpl = $this->smarty->create_template('helloworld.tpl');
        $tpl->compile_template_source();
        $this->assertTrue(file_exists($tpl->get_compiled()->filepath));
    }

    public function testGetCachedTimestamp()
    {
        // create dummy cache file for the following test
        $this->smarty->caching = true;
        $this->smarty->cache_lifetime = 1000;
        $tpl = $this->smarty->create_template('helloworld.tpl');
        $tpl->fetch();
        $timestamp = $tpl->get_cached()->timestamp;

        $this->smarty = new \Smarty\Smarty();
        $this->smarty->caching = true;
        $this->smarty->cache_lifetime = 1000;

        $tpl = $this->smarty->create_template('helloworld.tpl');
        $this->assertTrue(is_integer($tpl->get_cached()->timestamp));
        $this->assertEquals($timestamp, $tpl->get_cached()->timestamp);
    }

    public function testGetRenderedTemplate()
    {
        $tpl = $this->smarty->create_template('helloworld.tpl');
        $this->assertEquals('hello world', $tpl->fetch());
    }

    public function testRelativeInclude()
    {
        $result = $this->smarty->fetch('relative.tpl');
        $this->assertStringContainsString('hello world', $result);
    }

    /**
     *
     * @run InSeparateProcess
     *
     *
     */
    public function testRelativeIncludeSub()
    {
        $result = $this->smarty->fetch('sub/relative.tpl');
        $this->assertStringContainsString('hello world', $result);
    }
    /**
     * test relative include fail
     */
    public function testRelativeIncludeFail()
    {
        $this->expectException(\Smarty\Exception::class);
        $this->expectExceptionMessage('Unable to');
        $this->smarty->fetch('relative_sub.tpl');
    }
    /**
     *
     *
     *
     * test relative include fail other dir
     */
    public function testRelativeIncludeFailOtherDir()
    {
        $this->expectException(\Smarty\Exception::class);
        $this->expectExceptionMessage('./hello.tpl');
        $this->smarty->add_template_dir('./templates_2');
        $this->smarty->fetch('relative_notexist.tpl');
    }

    /**
     *
     *
     *
     *
     */
    public function testRelativeFetch()
    {
        $this->smarty->set_template_dir([
                                          __DIR__ . '/does-not-exist/',
                                          __DIR__ . '/templates/sub/',
                                      ]);
        $this->smarty->security_policy = null;
        $this->assertEquals('hello world', $this->smarty->fetch('./relative.tpl'));
    }

    /**
     *
     *
     *
     *
     */
    public function testRelativeFetch2()
    {
        $this->smarty->set_template_dir([
                                         __DIR__ . '/does-not-exist/',
                                         __DIR__ . '/templates/sub/',
                                     ]);
        $this->smarty->security_policy = null;
        $this->assertEquals('hello world', $this->smarty->fetch('../helloworld.tpl'));
    }

    /**
     *
     *
     *
     *
     */
    public function testRelativeFetchCwd()
    {
        $cwd = getcwd();
        chdir(__DIR__ . '/templates/sub/');
        $dn = __DIR__;
        $this->smarty->set_compile_dir($dn . '/templates_c/');
        $this->smarty->set_cache_dir($dn . '/cache/');
        $this->smarty->set_template_dir([
                                          __DIR__ . '/does-not-exist/',
                                      ]);
        $this->smarty->security_policy = null;
        $this->assertEquals('hello world', $this->smarty->fetch('./relative.tpl'));
        chdir($cwd);
    }

    /**
     *
     *
     *
     *
     */
    public function testRelativeFetchCwd2()
    {
        $cwd = getcwd();
        chdir(__DIR__ . '/templates/sub/');
        $dn = __DIR__;
        $this->smarty->set_compile_dir($dn . '/templates_c/');
        $this->smarty->set_cache_dir($dn . '/cache/');
        $this->smarty->set_template_dir([
                                          __DIR__ . '/does-not-exist/',
                                      ]);
        $this->smarty->security_policy = null;
        $this->assertEquals('hello world', $this->smarty->fetch('../helloworld.tpl'));
        chdir($cwd);
    }

    protected function _relative_map($map, $cwd = null)
    {
        foreach ($map as $file => $result) {
            $this->clean_dir($this->smarty->get_compile_dir());
            $this->clean_dir($this->smarty->get_cache_dir());

            if ($result === null) {
                try {
                    $this->smarty->fetch($file);
                    if ($cwd !== null) {
                        chdir($cwd);
                    }

                    $this->fail('Exception expected for ' . $file);

                    return;
                } catch (Exception $e) {
                    // this was expected to fail
                }
            } else {
                try {
                    $_res = $this->smarty->fetch($file);
                    $this->assertEquals(str_replace("\r", '', $result), $_res, $file);
                } catch (Exception $e) {
                    if ($cwd !== null) {
                        chdir($cwd);
                    }

                    throw $e;
                }
            }
        }

        if ($cwd !== null) {
            chdir($cwd);
        }
    }

    public function testRelativity()
    {
        $this->smarty->security_policy = null;

        $cwd = getcwd();
        $dn = __DIR__;
        $this->smarty->set_compile_dir($dn . '/templates_c/');
        $this->smarty->set_cache_dir($dn . '/cache/');
        $this->smarty->set_template_dir([$dn . '/templates/relativity/theory/',]);

        $map = ['foo.tpl' => 'theory', './foo.tpl' => 'theory', '././foo.tpl' => 'theory',
                      '.././foo.tpl' => 'relativity', './../foo.tpl' => 'relativity',
                     'einstein/foo.tpl' => 'einstein', './einstein/foo.tpl' => 'einstein',
                     '../theory/einstein/foo.tpl' => 'einstein', 'templates/relativity/relativity.tpl' => 'relativity',
                     './templates/relativity/relativity.tpl' => 'relativity',];

        $this->_relative_map($map);
    }

    public function testRelativity2()
    {
        $this->smarty->security_policy = null;

        $cwd = getcwd();
        $dn = __DIR__;
        $this->smarty->set_compile_dir($dn . '/templates_c/');
        $this->smarty->set_cache_dir($dn . '/cache/');

        $this->smarty->set_template_dir([
                                          './templates/relativity/theory/',
                                      ]);

        $map = [
            'foo.tpl'                               => 'theory',
            './foo.tpl'                             => 'theory',
            '././foo.tpl'                           => 'theory',
            '../foo.tpl'                            => 'relativity',
            '.././foo.tpl'                          => 'relativity',
            './../foo.tpl'                          => 'relativity',
            'einstein/foo.tpl'                      => 'einstein',
            './einstein/foo.tpl'                    => 'einstein',
            '../theory/einstein/foo.tpl'            => 'einstein',
            'templates/relativity/relativity.tpl'   => 'relativity',
            './templates/relativity/relativity.tpl' => 'relativity',
        ];

        $this->_relative_map($map);
    }

    public function testRelativityCwd()
    {
        $this->smarty->security_policy = null;

        $cwd = getcwd();
        $dn = __DIR__;

        $this->smarty->set_compile_dir($dn . '/templates_c/');
        $this->smarty->set_cache_dir($dn . '/cache/');
        chdir($dn . '/templates/relativity/theory/');
        $this->smarty->set_template_dir([
                                          $dn . '/templates/',
                                      ]);

        $map = [
            'foo.tpl'                    => 'theory',
            './foo.tpl'                  => 'theory',
            '././foo.tpl'                => 'theory',
            '../foo.tpl'                 => 'relativity',
            '.././foo.tpl'               => 'relativity',
            './../foo.tpl'               => 'relativity',
            'einstein/foo.tpl'           => 'einstein',
            './einstein/foo.tpl'         => 'einstein',
            '../theory/einstein/foo.tpl' => 'einstein',
        ];

        $this->_relative_map($map, $cwd);
    }

    public function testRelativityPrecedence()
    {
        $this->smarty->security_policy = null;

        $cwd = getcwd();
        $dn = __DIR__;

        $this->smarty->set_compile_dir($dn . '/templates_c/');
        $this->smarty->set_cache_dir($dn . '/cache/');
        $this->smarty->set_template_dir([$dn . '/templates/relativity/theory/einstein/',]);

        $map = ['foo.tpl' => 'einstein', './foo.tpl' => 'einstein', '././foo.tpl' => 'einstein',
                     '../foo.tpl' => 'theory', '.././foo.tpl' => 'theory', './../foo.tpl' => 'theory',
                     '../../foo.tpl' => 'relativity',];

        chdir($dn . '/templates/relativity/theory/');
        $this->smarty->set_template_dir([$dn . '/templates/relativity/theory/einstein/',]);
        $this->_relative_map($map, $cwd);
    }

    public function testRelativityPrecedence2()
    {
        $this->smarty->security_policy = null;

        $cwd = getcwd();
        $dn = __DIR__;

        $this->smarty->set_compile_dir($dn . '/templates_c/');
        $this->smarty->set_cache_dir($dn . '/cache/');
        $this->smarty->set_template_dir([
                                          $dn . '/templates/relativity/theory/einstein/',
                                      ]);

        $map = [
            '../theory.tpl'           => 'theory',
            './theory.tpl'            => 'theory',
            '../../relativity.tpl'    => 'relativity',
            '../relativity.tpl'       => 'relativity',
            './einstein.tpl'          => 'einstein',
            'einstein/einstein.tpl'   => 'einstein',
            './einstein/einstein.tpl' => 'einstein',
        ];

        chdir($dn . '/templates/relativity/theory/');
        $this->smarty->set_template_dir([
                                          $dn . '/templates/relativity/theory/einstein/',
                                      ]);
        $this->_relative_map($map, $cwd);
    }

    public function testRelativityRelRel()
    {
        $this->smarty->security_policy = null;

        $cwd = getcwd();
        $dn = __DIR__;

        $this->smarty->set_compile_dir($dn . '/templates_c/');
        $this->smarty->set_cache_dir($dn . '/cache/');
        $this->smarty->set_template_dir(['../..',]);

        $map = ['foo.tpl' => 'relativity', './foo.tpl' => 'relativity', '././foo.tpl' => 'relativity',];

        chdir($dn . '/templates/relativity/theory/einstein');
        $this->smarty->set_template_dir(['../..',]);
        $this->_relative_map($map, $cwd);
    }

    public function testRelativityRelRel2()
    {
        $this->smarty->security_policy = null;

        $cwd = getcwd();
        $dn = __DIR__;

        $this->smarty->set_compile_dir($dn . '/templates_c/');
        $this->smarty->set_cache_dir($dn . '/cache/');
        $this->smarty->set_template_dir(['../..',]);

        $map =
            ['relativity.tpl' => 'relativity', './relativity.tpl' => 'relativity', 'theory/theory.tpl' => 'theory',
                  './theory/theory.tpl' => 'theory',];

        chdir($dn . '/templates/relativity/theory/einstein/');
        $this->smarty->set_template_dir(['../..',]);
        $this->_relative_map($map, $cwd);
    }
    public function testRelativityRelRel3()
    {
        $this->smarty->security_policy = null;

        $cwd = getcwd();
        $dn = __DIR__;

        $this->smarty->set_compile_dir($dn . '/templates_c/');
        $this->smarty->set_cache_dir($dn . '/cache/');
        $this->smarty->set_template_dir([
                                          '../..',
                                      ]);

        $map = [
            'foo.tpl'                         => 'theory',
            './foo.tpl'                       => 'theory',
            'theory.tpl'                      => 'theory',
            './theory.tpl'                    => 'theory',
            'einstein/einstein.tpl'           => 'einstein',
            './einstein/einstein.tpl'         => 'einstein',
            '../theory/einstein/einstein.tpl' => 'einstein',
            '../relativity.tpl'               => 'relativity',
            './../relativity.tpl'             => 'relativity',
            '.././relativity.tpl'             => 'relativity',
        ];

        chdir($dn . '/templates/relativity/theory/einstein/');
        $this->smarty->set_template_dir([
                                          '..',
                                      ]);
        $this->_relative_map($map, $cwd);
    }

    public function testRelativityRelRel1()
    {
        $this->smarty->security_policy = null;

        $cwd = getcwd();
        $dn = __DIR__;
        $this->smarty->set_compile_dir($dn . '/templates_c/');
        $this->smarty->set_cache_dir($dn . '/cache/');
        $this->smarty->set_template_dir([
                                          '..',
                                      ]);

        $map = [
            'foo.tpl'                         => 'theory',
            './foo.tpl'                       => 'theory',
            'theory.tpl'                      => 'theory',
            './theory.tpl'                    => 'theory',
            'einstein/einstein.tpl'           => 'einstein',
            './einstein/einstein.tpl'         => 'einstein',
            '../theory/einstein/einstein.tpl' => 'einstein',
            '../relativity.tpl'               => 'relativity',
            './../relativity.tpl'             => 'relativity',
            '.././relativity.tpl'             => 'relativity',
        ];

        chdir($dn . '/templates/relativity/theory/einstein/');
        $this->smarty->set_template_dir([
                                          '..',
                                      ]);
        $this->_relative_map($map, $cwd);
    }

    /**
     * test {$smarty.template}
     *
     */
    public function testSmartyTemplate()
    {
        $this->assertEquals('template = 001_smarty_template.tpl', $this->smarty->fetch('001_smarty_template.tpl'));
    }
    /**
     * test {$smarty.current_dir}
     *
     */
    public function testSmartyCurrentDir()
    {
        $dirname = __DIR__ . DIRECTORY_SEPARATOR . 'templates';
        $this->assertEquals('current_dir = ' . $dirname, $this->smarty->fetch('001_smarty_current_dir.tpl'));
    }
}
