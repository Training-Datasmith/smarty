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
class Plugin_Modifier_Replace_Test extends \PHPUnit_Smarty
{
    public function setUp(): void
    {
        $this->set_up_smarty(__DIR__);
    }

    /**
     * @return void
     * @throws \Smarty\Exception
     *
     * @dataProvider replace_data_provider
     */
    public function testReplace($template, $subject, $expected_String)
    {
        $this->smarty->assign('subject', $subject);

        $tpl = $this->smarty->create_template($template);

        $this->assertEquals($expected_String, $this->smarty->fetch($tpl));
    }

    public function replace_data_provider()
    {
        return [
            'default'  => [
                'template'      => 'string:{$subject|replace:",":"-"}',
                'subject'       => 'a,b,c,d',
                'expectedString' => 'a-b-c-d',
            ],
            'doNothing'  => [
                'template'      => 'string:{$subject|replace:"":""}',
                'subject'       => 'a,b,c,d',
                'expectedString' => 'a,b,c,d',
            ],
            'withNull' => [
                'template'      => 'string:{$subject|replace:"":""}',
                'subject'       => null,
                'expectedString' => '',
            ],
        ];
    }
}
