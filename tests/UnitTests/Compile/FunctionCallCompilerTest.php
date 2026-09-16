<?php

declare(strict_types=1);

use Smarty\Compile\FunctionCallCompiler;
use Smarty\FunctionHandler\AttributeFunctionHandlerInterface;
use Smarty\FunctionHandler\FunctionHandlerInterface;
use Smarty\Smarty;

require_once __DIR__ . '/TemplateCompilerTestStub.php';

class FunctionCallCompilerTest extends PHPUnit\Framework\TestCase
{
    private Smarty $smarty;

    private TemplateCompilerTestStub $template_compiler;

    protected function setUp(): void
    {
        $handler = new class implements AttributeFunctionHandlerInterface {
            public function handle($params, \Smarty\Template $template)
            {
                return '';
            }

            public function isCacheable(): bool
            {
                return true;
            }

            public function getSupportedAttributes(): array
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
            public function __construct(private FunctionHandlerInterface $testHandler)
            {
                parent::__construct();
            }

            public function getFunctionHandler(string $functionName): ?\Smarty\FunctionHandler\FunctionHandlerInterface
            {
                if ($functionName === 'method') {
                    return $this->testHandler;
                }

                return parent::getFunctionHandler($functionName);
            }
        };

        $this->template_compiler = new TemplateCompilerTestStub($this->smarty);
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

        $function_call_compiler = new FunctionCallCompiler();

        $this->assertEquals(
            '$_smarty_tpl->getSmarty()->getFunctionHandler(\'method\')->handle(array(\'short\'=>short,\'option\'=>1,\'optional\'=>optional,\'required\'=>required), $_smarty_tpl)',
            $function_call_compiler->compile($args, $this->template_compiler, [], null, 'method')
        );
    }
}
