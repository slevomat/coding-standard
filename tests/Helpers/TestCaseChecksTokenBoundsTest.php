<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

use const T_OPEN_TAG;

class TestCaseChecksTokenBoundsTest extends TestCase
{

	public function testThrowsTokenPointerOutOfBoundsException(): void
	{
		try {
			$phpcsFile = $this->getCodeSnifferFile(__DIR__ . '/data/emptyPhpFile.php');
			self::assertTokenPointer(T_OPEN_TAG, 1, $phpcsFile, 5);
			self::fail();
		} catch (TokenPointerOutOfBoundsException $e) {
			self::assertStringStartsWith('Attempted access to token pointer 5, last token pointer', $e->getMessage());
			self::assertSame(5, $e->getPointer());
			self::assertPointer(1, $e->getLastTokenPointer());
		}
	}

}
