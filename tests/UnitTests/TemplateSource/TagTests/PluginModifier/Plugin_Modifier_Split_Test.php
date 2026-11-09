<?php

declare(strict_types=1);

namespace UnitTests\TemplateSource\TagTests\PluginModifier;

/**
 * class for modifier tests
 *
 *
 *
 *
 */
class Plugin_Modifier_Split_Test extends \PHPUnit_Smarty
{
    public function setUp(): void
    {
        $this->set_up_smarty(__DIR__);
        $this->smarty->register_plugin('modifier', 'json_encode', 'json_encode');
    }

    /**
     * @dataProvider explode_data_provider
     */
    public function testSplit($template, $subject, $expected_String)
    {
        $this->smarty->assign('subject', $subject);

        $tpl = $this->smarty->create_template($template);
        $res = $this->smarty->fetch($tpl);

        $this->assertEquals($expected_String, $res);
    }

    public function explode_data_provider()
    {
        return [
            'default'  => [
                'template'      => 'string:{$subject|split:","|json_encode}',
                'subject'       => 'a,b,c,d',
                'expectedString' => '["a","b","c","d"]',
            ],
            'withNoDelimiterFound'  => [
                'template'      => 'string:{$subject|split:","|json_encode}',
                'subject'       => 'abcd',
                'expectedString' => '["abcd"]',
            ],
            'withNull' => [
                'template'      => 'string:{$subject|split:","|json_encode}',
                'subject'       => null,
                'expectedString' => '[""]',
            ],
        ];
    }
}
