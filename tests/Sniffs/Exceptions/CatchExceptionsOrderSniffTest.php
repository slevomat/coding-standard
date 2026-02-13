<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Exceptions;

use SlevomatCodingStandard\Sniffs\TestCase;

class CatchExceptionsOrderSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/catchExceptionsOrderNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/catchExceptionsOrderErrors.php');

		self::assertSame(11, $report->getErrorCount());

		self::assertSniffError($report, 8, CatchExceptionsOrderSniff::CODE_INCORRECT_ORDER);
		self::assertSniffError($report, 14, CatchExceptionsOrderSniff::CODE_INCORRECT_ORDER);
		self::assertSniffError($report, 20, CatchExceptionsOrderSniff::CODE_INCORRECT_ORDER);
		self::assertSniffError($report, 26, CatchExceptionsOrderSniff::CODE_INCORRECT_ORDER);
		self::assertSniffError($report, 32, CatchExceptionsOrderSniff::CODE_INCORRECT_ORDER);
		self::assertSniffError($report, 42, CatchExceptionsOrderSniff::CODE_INCORRECT_ORDER);
		self::assertSniffError($report, 51, CatchExceptionsOrderSniff::CODE_INCORRECT_ORDER);
		self::assertSniffError($report, 60, CatchExceptionsOrderSniff::CODE_INCORRECT_ORDER);
		self::assertSniffError($report, 66, CatchExceptionsOrderSniff::CODE_INCORRECT_ORDER);
		self::assertSniffError($report, 68, CatchExceptionsOrderSniff::CODE_INCORRECT_ORDER);
		self::assertSniffError($report, 70, CatchExceptionsOrderSniff::CODE_INCORRECT_ORDER);

		self::assertAllFixedInFile($report);
	}

	public function testCaseSensitiveNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/catchExceptionsOrderCaseSensitiveNoErrors.php', [
			'caseSensitive' => true,
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testCaseSensitiveErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/catchExceptionsOrderCaseSensitiveErrors.php', [
			'caseSensitive' => true,
		]);

		self::assertSame(2, $report->getErrorCount());

		self::assertSniffError($report, 5, CatchExceptionsOrderSniff::CODE_INCORRECT_ORDER);
		self::assertSniffError($report, 11, CatchExceptionsOrderSniff::CODE_INCORRECT_ORDER);

		self::assertAllFixedInFile($report);
	}

}
