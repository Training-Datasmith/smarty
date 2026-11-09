<?php

declare(strict_types=1);
/*
 * This file is part of the Smarty PHPUnit tests.
 */

/**
 * class for path normalization tests
 *
 *
 * @preserveGlobalState    disabled
 *
 */
class Temlate_Dir_Normalization_Test extends PHPUnit_Smarty
{
    /*
     * Setup test fixture
     */
    public function setUp(): void
    {
        $this->set_up_smarty(__DIR__);
    }

    public function testGetTemplateDir()
    {
        $result = $this->smarty->get_template_dir();
        $this->assertEquals(1, count($result));
        $this->assertEquals(getcwd() . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR, $result[ 0 ]);
    }

    public function testSetTemplateDir1()
    {
        $result = $this->smarty->get_template_dir();
        $this->assertEquals(1, count($result));
        $this->smarty->set_template_dir('foo');
        $result = $this->smarty->get_template_dir();
        $this->assertEquals(1, count($result));
        $this->assertEquals(getcwd() . DIRECTORY_SEPARATOR . 'foo' . DIRECTORY_SEPARATOR, $result[ 0 ]);
    }

    public function testSetTemplateDir2()
    {
        $result = $this->smarty->get_template_dir();
        $this->assertEquals(1, count($result));
        $this->smarty->set_template_dir('foo/');
        $result = $this->smarty->get_template_dir();
        $this->assertEquals(1, count($result));
        $this->assertEquals(getcwd() . DIRECTORY_SEPARATOR . 'foo' . DIRECTORY_SEPARATOR, $result[ 0 ]);
    }

    public function testSetTemplateDir3()
    {
        $result = $this->smarty->get_template_dir();
        $this->assertEquals(1, count($result));
        $this->smarty->set_template_dir('foo/.');
        $result = $this->smarty->get_template_dir();
        $this->assertEquals(1, count($result));
        $this->assertEquals(getcwd() . DIRECTORY_SEPARATOR . 'foo' . DIRECTORY_SEPARATOR, $result[ 0 ]);
    }

    public function testSetTemplateDir4()
    {
        $result = $this->smarty->get_template_dir();
        $this->assertEquals(1, count($result));
        $this->smarty->set_template_dir('foo/./');
        $result = $this->smarty->get_template_dir();
        $this->assertEquals(1, count($result));
        $this->assertEquals(getcwd() . DIRECTORY_SEPARATOR . 'foo' . DIRECTORY_SEPARATOR, $result[ 0 ]);
    }

    public function testSetTemplateDir5()
    {
        $result = $this->smarty->get_template_dir();
        $this->assertEquals(1, count($result));
        $this->smarty->set_template_dir('.foo');
        $result = $this->smarty->get_template_dir();
        $this->assertEquals(1, count($result));
        $this->assertEquals(getcwd() . DIRECTORY_SEPARATOR . '.foo' . DIRECTORY_SEPARATOR, $result[ 0 ]);
    }

    public function testSetTemplateDir6()
    {
        $result = $this->smarty->get_template_dir();
        $this->assertEquals(1, count($result));
        $this->smarty->set_template_dir('..foo');
        $result = $this->smarty->get_template_dir();
        $this->assertEquals(1, count($result));
        $this->assertEquals(getcwd() . DIRECTORY_SEPARATOR . '..foo' . DIRECTORY_SEPARATOR, $result[ 0 ]);
    }

    public function testSetTemplateDir7()
    {
        $result = $this->smarty->get_template_dir();
        $this->assertEquals(1, count($result));
        $this->smarty->set_template_dir('../foo');
        $result = $this->smarty->get_template_dir();
        $this->assertEquals(1, count($result));
        $this->assertEquals(
            substr(getcwd(), 0, - strlen(basename(getcwd()))) . 'foo' . DIRECTORY_SEPARATOR,
            $result[ 0 ]
        );
    }

    public function testSetTemplateDir8()
    {
        $this->smarty->set_template_dir(['foo', 'bah']);
        $result = $this->smarty->get_template_dir();
        $this->assertEquals(2, count($result));
        $this->assertEquals(getcwd() . DIRECTORY_SEPARATOR . 'foo' . DIRECTORY_SEPARATOR, $result[ 0 ]);
        $this->assertEquals(getcwd() . DIRECTORY_SEPARATOR . 'bah' . DIRECTORY_SEPARATOR, $result[ 1 ]);
    }

    public function testSetTemplateDir9()
    {
        $this->smarty->set_template_dir(['foo', 'bah']);
        $result = $this->smarty->get_template_dir();
        $this->assertEquals(2, count($result));
        $this->smarty->add_template_dir('blar');
        $result = $this->smarty->get_template_dir();
        $this->assertEquals(3, count($result));
        $this->assertEquals(getcwd() . DIRECTORY_SEPARATOR . 'foo' . DIRECTORY_SEPARATOR, $result[ 0 ]);
        $this->assertEquals(getcwd() . DIRECTORY_SEPARATOR . 'bah' . DIRECTORY_SEPARATOR, $result[ 1 ]);
        $this->assertEquals(getcwd() . DIRECTORY_SEPARATOR . 'blar' . DIRECTORY_SEPARATOR, $result[ 2 ]);
    }

    public function testSetTemplateDir10()
    {
        $this->smarty->set_template_dir(['foo', 'bah']);
        $result = $this->smarty->get_template_dir();
        $this->assertEquals(2, count($result));
        $this->smarty->add_template_dir(['blar', 'smarty']);
        $result = $this->smarty->get_template_dir();
        $this->assertEquals(4, count($result));
        $this->assertEquals(getcwd() . DIRECTORY_SEPARATOR . 'foo' . DIRECTORY_SEPARATOR, $result[ 0 ]);
        $this->assertEquals(getcwd() . DIRECTORY_SEPARATOR . 'bah' . DIRECTORY_SEPARATOR, $result[ 1 ]);
        $this->assertEquals(getcwd() . DIRECTORY_SEPARATOR . 'blar' . DIRECTORY_SEPARATOR, $result[ 2 ]);
        $this->assertEquals(getcwd() . DIRECTORY_SEPARATOR . 'smarty' . DIRECTORY_SEPARATOR, $result[ 3 ]);
    }

}
