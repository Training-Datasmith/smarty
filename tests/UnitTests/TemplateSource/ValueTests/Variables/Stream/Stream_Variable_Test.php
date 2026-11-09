<?php

declare(strict_types=1);
/**
 * Smarty PHPunit tests stream variables
 *

 * @author  Uwe Tews
 */

/**
 * class for stream variables tests
 *
 *
 *
 *
 */
class Stream_Variable_Test extends PHPUnit_Smarty
{
    public function setUp(): void
    {
        $this->set_up_smarty(__DIR__);

        stream_wrapper_register('var', 'Variable_Stream')
        or die('Failed to register protocol');
        $fp = fopen('var://foo', 'r+');
        fwrite($fp, 'hello world');
        fclose($fp);
    }

    public function testInit()
    {
        $this->clean_dirs();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        stream_wrapper_unregister('var');
    }

    /**
     * test stream variable
     */
    public function testStreamVariable1()
    {
        $tpl = $this->smarty->create_template('eval:{$var:foo}', null, null, $this->smarty);
        $this->assertEquals('hello world', $this->fetch_with_user_deprecation('stream variables', $tpl));
    }
    /*
        public function testStreamVariable2()
        {
            $tpl = $this->smarty->create_template('eval:{var:\'foo\'}', null, null, $this->smarty);
            $this->assertEquals('hello world', $this->fetch_with_user_deprecation('stream variables', $tpl));
        }

        public function testStreamVariable3()
        {
            $tpl = $this->smarty->create_template('eval:{var:"foo"}', null, null, $this->smarty);
            $this->assertEquals('hello world', $this->fetch_with_user_deprecation('stream variables', $tpl));
        }
    */
    /**
     * test no existent stream variable
     */
    //    public function testStreamVariable2()
    //    {
    //        $tpl = $this->smarty->create_template('eval:{$var:bar}', null, null, $this->smarty);
    //        $this->assertEquals('', $this->fetch_with_user_deprecation('stream variables', $tpl));
    //    }
}

#[AllowDynamicProperties]
class Variable_Stream
{
    private $position;
    private $varname;

    public function stream_open($path, $mode, $options, &$opened_path)
    {
        $url = parse_url($path);
        $this->varname = $url['host'];
        $this->position = 0;

        return true;
    }

    public function stream_read($count)
    {
        $p = &$this->position;
        $ret = substr($GLOBALS[$this->varname], $p, $count);
        $p += strlen($ret);

        return $ret;
    }

    public function stream_write($data)
    {
        $v = &$GLOBALS[$this->varname];
        $l = strlen($data);
        $p = &$this->position;
        $v = substr($v ?? '', 0, $p) . $data . substr($v ?? '', $p += $l);

        return $l;
    }

    public function stream_tell()
    {
        return $this->position;
    }

    public function stream_eof()
    {
        return $this->position >= strlen($GLOBALS[$this->varname]);
    }

    public function stream_seek($offset, $whence)
    {
        $l = strlen($GLOBALS[$this->varname]);
        $p = &$this->position;
        switch ($whence) {
            case SEEK_SET:
                $new_Pos = $offset;
                break;
            case SEEK_CUR:
                $new_Pos = $p + $offset;
                break;
            case SEEK_END:
                $new_Pos = $l + $offset;
                break;
            default:
                return false;
        }
        $ret = ($new_Pos >= 0 && $new_Pos <= $l);
        if ($ret) {
            $p = $new_Pos;
        }
        return $ret;
    }
}
