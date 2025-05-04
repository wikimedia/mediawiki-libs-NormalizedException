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

		// Test default values
		$exception = new NormalizedException( 'foo' );
		$this->assertSame( 'foo', $exception->getNormalizedMessage() );
		$this->assertSame( [], $exception->getMessageContext() );
		$this->assertSame( 0, $exception->getCode() );
		$this->assertNull( $exception->getPrevious() );
	}

	/** @dataProvider provideMessage */
	public function testMessage( $normalizedMessage, $context, $expectedMessage ) {
		$exception = new NormalizedException( $normalizedMessage, $context );
		$this->assertSame( $expectedMessage, $exception->getMessage() );
	}

	public static function provideMessage() {
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

	public function testSubclass() {
		if ( PHP_MAJOR_VERSION === 7 ) {
			$this->markTestSkipped( 'Fails on PHP 7.4 - T384905' );
		}
		$e = new NormalizedExceptionSubclass( 'foo', 'message {a} {b}', [ 'a' => 1, 'b' => 2 ] );
		$this->assertSame( 'message 1 2', $e->getMessage() );
	}

}
