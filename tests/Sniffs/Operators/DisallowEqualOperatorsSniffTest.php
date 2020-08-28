<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Operators;

use SlevomatCodingStandard\Sniffs\TestCase;

class DisallowEqualOperatorsSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/disallowEqualOperatorsNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/disallowEqualOperatorsErrors.php');

		self::assertSame(5, $report->getErrorCount());

		self::assertSniffError($report, 3, DisallowEqualOperatorsSniff::CODE_DISALLOWED_EQUAL_OPERATOR);
		self::assertSniffError($report, 4, DisallowEqualOperatorsSniff::CODE_DISALLOWED_EQUAL_OPERATOR);
		self::assertSniffError(
			$report,
			5,
			DisallowEqualOperatorsSniff::CODE_DISALLOWED_NOT_EQUAL_OPERATOR,
			'Operator != is disallowed, use !== instead.'
		);
		self::assertSniffError($report, 6, DisallowEqualOperatorsSniff::CODE_DISALLOWED_NOT_EQUAL_OPERATOR);
		self::assertSniffError(
			$report,
			7,
			DisallowEqualOperatorsSniff::CODE_DISALLOWED_NOT_EQUAL_OPERATOR,
			'Operator <> is disallowed, use !== instead.'
		);

		self::assertAllFixedInFile($report);
	}

}
