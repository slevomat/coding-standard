<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

class TestCaseChecksTokenBoundsTest extends \SlevomatCodingStandard\Helpers\TestCase
{

	public function testThrowsTokenPointerOutOfBoundsException(): void
	{
		try {
			$codeSnifferFile = $this->getCodeSnifferFile(
				__DIR__ . '/data/emptyPhpFile.php'
			);
			self::assertTokenPointer(T_OPEN_TAG, 1, $codeSnifferFile, 5);
			$this->fail();
		} catch (\SlevomatCodingStandard\Helpers\TokenPointerOutOfBoundsException $e) {
			self::assertSame('Attempted access to token pointer 5, last token pointer is 0', $e->getMessage());
			self::assertSame(5, $e->getPointer());
			self::assertSame(0, $e->getLastTokenPointer());
		}
	}

}
