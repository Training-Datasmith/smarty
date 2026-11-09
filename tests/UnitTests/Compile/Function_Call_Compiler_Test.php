<?php

declare(strict_types=1);

use Smarty\Compile\Function_Call_Compiler;
use Smarty\Function_Handler\Attribute_Function_Handler_Interface;
use Smarty\Function_Handler\Function_Handler_Interface;
use Smarty\Smarty;

require_once __DIR__ . '/Template_Compiler_Test_Stub.php';

class Function_Call_Compiler_Test extends PHPUnit\Framework\TestCase
{
    private Smarty $smarty;

    private Template_Compiler_Test_Stub $template_compiler;

    protected function setUp(): void
    {
        $handler = new class implements Attribute_Function_Handler_Interface {
            public function handle($params, \Smarty\Template $template)
            {
                return '';
            }

            public function is_cacheable(): bool
            {
                return true;
            }

            public function get_supported_attributes(): array
            {
                return [
                    'required_attributes' => ['required'],
                    'optional_attributes' => ['optional', 'short'],
                    'shorttag_order' => ['short'],
                    'option_flags' => ['option'],
                ];
            }
        };

        $this->smarty = new class($handler) extends Smarty {
            public function __construct(private Function_Handler_Interface $testHandler)
            {
                parent::__construct();
            }

            public function get_function_handler(string $functionName): ?\Smarty\Function_Handler\Function_Handler_Interface
            {
                if ($functionName === 'method') {
                    return $this->testHandler;
                }

                return parent::get_function_handler($functionName);
            }
        };

        $this->template_compiler = new Template_Compiler_Test_Stub($this->smarty);
    }

    public function testAttributeFunctionHandlerInterface(): void
    {
        $args = [
            0 => 'short',
            1 => 'option',
            2 => [
                'optional' => 'optional',
            ],
            3 => [
                'required' => 'required',
            ],
        ];

        $function_call_compiler = new Function_Call_Compiler();

        $this->assertEquals(
            '$_smarty_tpl->get_smarty()->get_function_handler(\'method\')->handle(array(\'short\'=>short,\'option\'=>1,\'optional\'=>optional,\'required\'=>required), $_smarty_tpl)',
            $function_call_compiler->compile($args, $this->template_compiler, [], null, 'method')
        );
    }
}
