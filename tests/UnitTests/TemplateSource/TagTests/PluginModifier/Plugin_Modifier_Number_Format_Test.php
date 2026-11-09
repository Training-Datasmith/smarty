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
class Plugin_Modifier_Number_Format_Test extends \PHPUnit_Smarty
{
    public function setUp(): void
    {
        $this->set_up_smarty(__DIR__);
    }

    /**
     * @return void
     * @throws \Smarty\Exception
     *
     * @dataProvider number_format_data_provider
     */
    public function testNumberFormat($template, $subject, $expected_String)
    {
        $this->smarty->assign('subject', $subject);

        $tpl = $this->smarty->create_template($template);

        $this->assertEquals($expected_String, $this->smarty->fetch($tpl));
    }

    public function number_format_data_provider()
    {
        return [
            'default'  => [
                'template'      => 'string:{$subject|number_format}',
                'subject'       => 12345,
                'expectedString' => '12,345',
            ],
            'withDecimalDefault'  => [
                'template'      => 'string:{$subject|number_format}',
                'subject'       => 12345.6789,
                'expectedString' => '12,346',
            ],
            'withDecimalAndExtras'  => [
                'template'      => 'string:{$subject|number_format:2:"-":"~"}',
                'subject'       => 12345.6789,
                'expectedString' => '12~345-68',
            ],
            'withNull' => [
                'template'      => 'string:{$subject|number_format}',
                'subject'       => null,
                'expectedString' => 0,
            ],
        ];
    }
}
