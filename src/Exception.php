<?php

namespace Smarty;

/**
 * Smarty exception class
 */
class Exception extends \Exception {

	public function __toString(): string {
		return ' --> Smarty: ' . $this->message . ' <-- ';
	}
}
