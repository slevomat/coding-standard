<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\PHP;

use SlevomatCodingStandard\Sniffs\TestCase;

class DisallowDirectMagicInvokeCallSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/disallowDirectMagicInvokeCallNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/disallowDirectMagicInvokeCallErrors.php');

		self::assertSame(10, $report->getErrorCount());

		self::assertSniffError($report, 3, DisallowDirectMagicInvokeCallSniff::CODE_DISALLOWED_DIRECT_MAGIC_INVOKE_CALL);
		self::assertSniffError($report, 7, DisallowDirectMagicInvokeCallSniff::CODE_DISALLOWED_DIRECT_MAGIC_INVOKE_CALL);
		self::assertSniffError($report, 9, DisallowDirectMagicInvokeCallSniff::CODE_DISALLOWED_DIRECT_MAGIC_INVOKE_CALL);
		self::assertSniffError($report, 11, DisallowDirectMagicInvokeCallSniff::CODE_DISALLOWED_DIRECT_MAGIC_INVOKE_CALL);
		self::assertSniffError($report, 13, DisallowDirectMagicInvokeCallSniff::CODE_DISALLOWED_DIRECT_MAGIC_INVOKE_CALL);
		self::assertSniffError($report, 15, DisallowDirectMagicInvokeCallSniff::CODE_DISALLOWED_DIRECT_MAGIC_INVOKE_CALL);
		self::assertSniffError($report, 17, DisallowDirectMagicInvokeCallSniff::CODE_DISALLOWED_DIRECT_MAGIC_INVOKE_CALL);
		self::assertSniffError($report, 19, DisallowDirectMagicInvokeCallSniff::CODE_DISALLOWED_DIRECT_MAGIC_INVOKE_CALL);
		self::assertSniffError($report, 21, DisallowDirectMagicInvokeCallSniff::CODE_DISALLOWED_DIRECT_MAGIC_INVOKE_CALL);
		self::assertSniffError($report, 23, DisallowDirectMagicInvokeCallSniff::CODE_DISALLOWED_DIRECT_MAGIC_INVOKE_CALL);

		self::assertAllFixedInFile($report);
	}

}
