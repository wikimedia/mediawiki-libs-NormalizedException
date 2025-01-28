<?php

namespace Wikimedia\NormalizedException\Tests;

use Wikimedia\NormalizedException\NormalizedException;

class NormalizedExceptionSubclass extends NormalizedException {
	public function __construct( string $foo, string $message, array $context = [] ) {
		parent::__construct( $message, $context );
	}
}
