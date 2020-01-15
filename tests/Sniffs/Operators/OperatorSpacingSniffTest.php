<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Operators;

use SlevomatCodingStandard\Sniffs\TestCase;

class OperatorSpacingSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		self::assertNoSniffErrorInFile(
			self::checkFile(
				__DIR__ . '/data/OperatorSpacingSniffNoErrors.php',
				['ignoreSpacingBeforeAssignments' => false]
			)
		);
	}

	public function testErrors(): void
	{
		$file = self::checkFile(
			__DIR__ . '/data/OperatorSpacingSniffErrors.php',
			['ignoreSpacingBeforeAssignments' => false]
		);

		self::assertSame(67, $file->getErrorCount());

		self::assertAllFixedInFile($file);
	}

}
