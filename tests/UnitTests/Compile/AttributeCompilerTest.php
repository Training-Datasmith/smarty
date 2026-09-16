<?php

declare(strict_types=1);

use Smarty\Compile\AttributeCompiler;

require_once __DIR__ . '/CompilerStub.php';

class AttributeCompilerTest extends PHPUnit\Framework\TestCase
{
    /**
     * The template compiler.
     */
    private $template_compiler;

    /**
     * The attributes
     */
    private $attributes = [];

    /**
     * @inheritDoc
     * Set up attribute compiler class
     */
    protected function setUp(): void
    {
        $this->template_compiler = new CompilerStub();

        // reset attributes to empty arrays
        $this->attributes = [
            'required_attributes' => [],
            'optional_attributes' => [],
            'shorttag_order' => [],
            'option_flags' => [],
        ];
    }

    /**
     * Create the attribute compiler for testing.
     */
    private function createAttributeCompiler()
    {
        return new AttributeCompiler(
            $this->attributes['required_attributes'],
            $this->attributes['optional_attributes'],
            $this->attributes['shorttag_order'],
            $this->attributes['option_flags']
        );
    }

    /**
     * Tests shorthand attribute compiling.
     */
    public function testAttributeCompiler(): void
    {
        $this->attributes['shorttag_order'] = ['shorttag'];
        $this->attributes['required_attributes'] = ['required'];
        $this->attributes['optional_attributes'] = ['shorttag'];
        $this->attributes['option_flags'] = ['option', 'option_two'];

        $payload = [
            0 => 'shorttag value',
            1 => [
                'required' => 'required_value',
            ],
            2 => 'option',
        ];

        $this->assertEquals(
            [
                'shorttag' => 'shorttag value',
                'required' => 'required_value',
                'option' => true,
                'option_two' => false,
            ],
            $this->createAttributeCompiler()
                ->getAttributes($this->template_compiler, $payload)
        );
    }

    /**
     * Tests normal optional attribute compiling.
     */
    public function testAttributeCompilerOptionalArguments(): void
    {
        $this->attributes['optional_attributes'] = ['optional'];

        $payload = [
            0 => [
                'optional' => 'optional value',
            ],
        ];

        $this->assertEquals(
            [
                'optional' => 'optional value',
            ],
            $this->createAttributeCompiler()
                ->getAttributes($this->template_compiler, $payload)
        );

        $this->assertEquals(
            [],
            $this->createAttributeCompiler()
                ->getAttributes($this->template_compiler, [])
        );
    }

    /**
     * Tests any attribute compiling.
     */
    public function testAttributeCompilerAnyOptionalArguments(): void
    {
        $this->attributes['optional_attributes'] = ['_any'];

        $payload = [
            0 => [
                'optional' => 'optional value',
            ],
            1 => [
                'optional_two' => 'optional value two',
            ],
        ];

        $this->assertEquals(
            [
                'optional' => 'optional value',
                'optional_two' => 'optional value two',
            ],
            $this->createAttributeCompiler()
                ->getAttributes($this->template_compiler, $payload)
        );
    }

    /**
     * Test if the attribute compiler tries to throw a too many shorthand attributes error.
     */
    public function testAttributeCompilerTooManyShorthands(): void
    {
        $payload = [
            0 => 'option one',
        ];

        $this->expectException(\Smarty\Exception::class);
        $this->expectExceptionMessage('too many shorthand attributes');

        $this->createAttributeCompiler()
            ->getAttributes($this->template_compiler, $payload);
    }

    /**
     * Test if the attribute compiler tries to throw a missing required attribute error.
     */
    public function testAttributeCompilerWithMissingRequiredAttributes(): void
    {
        $this->attributes['required_attributes'] = ['required'];

        $this->expectException(\Smarty\Exception::class);
        $this->expectExceptionMessage('missing \'required\' attribute');

        $this->createAttributeCompiler()
            ->getAttributes($this->template_compiler, []);
    }

    /**
     * Test if the attribute compiler tries to throw a illegal value template error.
     */
    public function testAttributeCompilerWithInvalidOptionAttribute(): void
    {
        $this->attributes['option_flags'] = ['option'];

        $this->expectException(\Smarty\Exception::class);
        $this->expectExceptionMessage('illegal value');

        $this->createAttributeCompiler()
            ->getAttributes($this->template_compiler, [0 => ['option' => 'foo']]);
    }

    /**
     * Test if the attribute compiler tries to throw an unexpected attribute error.
     */
    public function testAttributeCompilerWithInvalidUnexpectedAttribute(): void
    {
        $this->expectException(\Smarty\Exception::class);
        $this->expectExceptionMessage('unexpected \'unexpected\' attribute');

        $this->createAttributeCompiler()
            ->getAttributes($this->template_compiler, [0 => ['unexpected' => 'bar']]);
    }
}
