<?php

declare(strict_types=1);

/**
 * Minimal compiler stand-in for unit tests (avoids mocking Smarty\Compiler\Template).
 */
class CompilerStub
{
    public function trigger_template_error($message, $line = null, $throw = false): void
    {
        if ($throw) {
            throw new \Smarty\Exception((string) $message);
        }
    }
}
