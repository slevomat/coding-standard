<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Exceptions;

use SlevomatCodingStandard\Sniffs\TestCase;

class DeadCatchSniffTest extends TestCase
{

	public function testNoDeadCatches(): void
	{
		$report = self::checkFile(__DIR__ . '/data/noDeadCatches.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testDeadCatchesWithoutNamespace(): void
	{
		$report = self::checkFile(__DIR__ . '/data/deadCatchesWithoutNamespace.php');

		self::assertSame(6, $report->getErrorCount());

		self::assertSniffError($report, 9, DeadCatchSniff::CODE_CATCH_AFTER_THROWABLE_CATCH);
		self::assertSniffError($report, 11, DeadCatchSniff::CODE_CATCH_AFTER_THROWABLE_CATCH);
		self::assertSniffError($report, 23, DeadCatchSniff::CODE_CATCH_AFTER_THROWABLE_CATCH);
		self::assertSniffError($report, 33, DeadCatchSniff::CODE_CATCH_AFTER_THROWABLE_CATCH);
		self::assertSniffError($report, 35, DeadCatchSniff::CODE_CATCH_AFTER_THROWABLE_CATCH);
		self::assertSniffError($report, 47, DeadCatchSniff::CODE_CATCH_AFTER_THROWABLE_CATCH);
	}

	public function testDeadCatchesInNamespace(): void
	{
		$report = self::checkFile(__DIR__ . '/data/deadCatches.php');

		self::assertSame(4, $report->getErrorCount());

		self::assertSniffError($report, 49, DeadCatchSniff::CODE_CATCH_AFTER_THROWABLE_CATCH);
		self::assertSniffError($report, 51, DeadCatchSniff::CODE_CATCH_AFTER_THROWABLE_CATCH);
		self::assertSniffError($report, 61, DeadCatchSniff::CODE_CATCH_AFTER_THROWABLE_CATCH);
		self::assertSniffError($report, 63, DeadCatchSniff::CODE_CATCH_AFTER_THROWABLE_CATCH);
	}

	public function testDeadUnionCatches(): void
	{
		$report = self::checkFile(__DIR__ . '/data/deadUnionCatches.php');

		self::assertSame(2, $report->getErrorCount());

		self::assertSniffError($report, 31, DeadCatchSniff::CODE_CATCH_AFTER_THROWABLE_CATCH);
		self::assertSniffError($report, 41, DeadCatchSniff::CODE_CATCH_AFTER_THROWABLE_CATCH);
	}

	public function testDeadCatchWeirdDefinition(): void
	{
		$report = self::checkFile(__DIR__ . '/data/deadCatchesWeirdDefinition.php');

		self::assertSame(2, $report->getErrorCount());

		self::assertSniffError($report, 13, DeadCatchSniff::CODE_CATCH_AFTER_THROWABLE_CATCH);
		self::assertSniffError($report, 21, DeadCatchSniff::CODE_CATCH_AFTER_THROWABLE_CATCH);
	}

}
