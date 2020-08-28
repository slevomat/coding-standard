<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\ControlStructures;

use SlevomatCodingStandard\Sniffs\TestCase;

class DisallowShortTernaryOperatorSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/disallowShortTernaryOperatorNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/disallowShortTernaryOperatorErrors.php');

		self::assertSame(3, $report->getErrorCount());

		self::assertSniffError($report, 3, DisallowShortTernaryOperatorSniff::CODE_DISALLOWED_SHORT_TERNARY_OPERATOR);
		self::assertSniffError($report, 5, DisallowShortTernaryOperatorSniff::CODE_DISALLOWED_SHORT_TERNARY_OPERATOR);
		self::assertSniffError($report, 7, DisallowShortTernaryOperatorSniff::CODE_DISALLOWED_SHORT_TERNARY_OPERATOR);

		self::assertAllFixedInFile($report);
	}

	public function testFixableDisabled(): void
	{
		$report = self::checkFile(__DIR__ . '/data/disallowShortTernaryOperatorErrorsFixableDisabled.php', ['fixable' => false]);

		self::assertSame(3, $report->getErrorCount());

		self::assertSniffError($report, 3, DisallowShortTernaryOperatorSniff::CODE_DISALLOWED_SHORT_TERNARY_OPERATOR);
		self::assertSniffError($report, 5, DisallowShortTernaryOperatorSniff::CODE_DISALLOWED_SHORT_TERNARY_OPERATOR);
		self::assertSniffError($report, 7, DisallowShortTernaryOperatorSniff::CODE_DISALLOWED_SHORT_TERNARY_OPERATOR);

		self::assertAllFixedInFile($report);
	}

}
