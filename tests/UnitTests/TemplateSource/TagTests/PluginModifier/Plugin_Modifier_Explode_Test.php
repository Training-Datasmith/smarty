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
class Plugin_Modifier_Explode_Test extends \PHPUnit_Smarty
{
    public function setUp(): void
    {
        $this->set_up_smarty(__DIR__);
        $this->smarty->register_plugin('modifier', 'json_encode', 'json_encode');
    }

    /**
     * @deprecated
     * @dataProvider explode_data_provider
     */
    public function testExplode($template, $subject, $expected_String)
    {
        $this->smarty->assign('subject', $subject);

        $tpl = $this->smarty->create_template($template);
        $res = $this->fetch_with_user_deprecation('Using explode is deprecated', $tpl);

        $this->assertEquals($expected_String, $res);
    }

    public function explode_data_provider()
    {
        return [
            'default'  => [
                'template'      => 'string:{","|explode:$subject|json_encode}',
                'subject'       => 'a,b,c,d',
                'expectedString' => '["a","b","c","d"]',
            ],
            'withNoDelimiterFound'  => [
                'template'      => 'string:{","|explode:$subject|json_encode}',
                'subject'       => 'abcd',
                'expectedString' => '["abcd"]',
            ],
            'withNull' => [
                'template'      => 'string:{","|explode:$subject|json_encode}',
                'subject'       => null,
                'expectedString' => '[""]',
            ],
        ];
    }
}
