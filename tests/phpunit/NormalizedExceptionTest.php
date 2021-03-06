<?php

namespace Wikimedia\NormalizedException\Tests;

use Exception;
use PHPUnit\Framework\TestCase;
use Wikimedia\NormalizedException\NormalizedException;

/**
 * @covers \Wikimedia\NormalizedException\NormalizedException
 */
class NormalizedExceptionTest extends TestCase {

	public function testConstruct() {
		$previousException = new Exception();
		$exception = new NormalizedException( 'foo', [ 'bar' => 'baz' ], 1, $previousException );
		$this->assertSame( 'foo', $exception->getNormalizedMessage() );
		$this->assertSame( [ 'bar' => 'baz' ], $exception->getMessageContext() );
		$this->assertSame( 1, $exception->getCode() );
		$this->assertSame( $previousException, $exception->getPrevious() );
	}

	/** @dataProvider provideMessage */
	public function testMessage( $normalizedMessage, $context, $expectedMessage ) {
		$exception = new NormalizedException( $normalizedMessage, $context );
		$this->assertSame( $expectedMessage, $exception->getMessage() );
	}

	public function provideMessage() {
		return [
			[ 'foo', [ 'bar' => 'baz' ], 'foo' ],
			[ '{foo}', [ 'bar' => 'baz' ], '{foo}' ],
			[ 'bar', [ 'bar' => 'baz' ], 'bar' ],
			[ '{bar}', [ 'bar' => 'baz' ], 'baz' ],
			[ 'a {b} c {d} e', [ 'b' => 'x', 'd' => 'y' ], 'a x c y e' ],
			[ 'a {b} c {d} e', [ 'd' => 'y', 'b' => 'x' ], 'a x c y e' ],
			[ '{v}', [ 'v' => 1 ], '1' ],
			[ '{v}', [ 'v' => 1.0 ], '1' ],
			[ '{v}', [ 'v' => 1.1 ], '1.1' ],
			[ '{v}', [ 'v' => null ], '<null>' ],
			[ '{v}', [ 'v' => true ], '<true>' ],
			[ '{v}', [ 'v' => false ], '<false>' ],
			[ '{a} {b}', [ 'a' => '{b}', 'b' => 'c' ], '{b} c' ],
		];
	}

}
