<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

use SlevomatCodingStandard\Sniffs\TestCase;

class ConstantSpacingSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		self::assertNoSniffErrorInFile(self::checkFile(__DIR__ . '/data/constantSpacingSniffNoErrors.php'));
	}

	public function testErrors(): void
	{
		$file = self::checkFile(__DIR__ . '/data/constantSpacingSniffErrors.php');

		self::assertSame(2, $file->getErrorCount());

		self::assertSniffError($file, 5, ConstantSpacingSniff::CODE_INCORRECT_COUNT_OF_BLANK_LINES_AFTER_CONSTANT);
		self::assertSniffError($file, 22, ConstantSpacingSniff::CODE_INCORRECT_COUNT_OF_BLANK_LINES_AFTER_CONSTANT);

		self::assertAllFixedInFile($file);
	}

}
