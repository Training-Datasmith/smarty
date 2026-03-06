<?php

declare(strict_types=1);

namespace Smarty\FunctionHandler;

use Smarty\Template;

/**
 * Abstract implementation for function handlers which support custom attributes
 */
abstract class AttributeBase implements AttributeFunctionHandlerInterface
{
    /**
     * Array of names of required attribute required by tag
     */
    protected array $required_attributes = [];

    /**
     * Array of names of optional attribute required by tag
     * use array('_any') if there is no restriction of attributes names
     */
    protected array $optional_attributes = [];

    /**
     * Shorttag attribute order defined by its names
     */
    protected array $shorttag_order = [];

    /**
     * Array of names of valid option flags
     */
    protected array $option_flags = [];

    /**
     * Return whether the output is cacheable.
     */
    protected bool $cacheable = true;

    /**
     * Return whether the output is cacheable.
     */
    public function isCacheable(): bool
    {
        return $this->cacheable;
    }

    /**
     * Function body
     * @param mixed $params The supplied parameters.
     */
    abstract public function handle($params, Template $template): ?string;

    /**
     * Return the support attributes for this function.
     * @return array<string, array>
     */
    public function getSupportedAttributes(): array
    {
        return [
            'required_attributes' => $this->required_attributes,
            'optional_attributes' => $this->optional_attributes,
            'shorttag_order' => $this->shorttag_order,
            'option_flags' => $this->option_flags,
        ];
    }
}
