<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Operators;

use SlevomatCodingStandard\Sniffs\TestCase;

class RequireCombinedAssignmentOperatorSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireCombinedAssignmentOperatorNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/requireCombinedAssignmentOperatorErrors.php');

		self::assertSame(15, $report->getErrorCount());

		self::assertSniffError(
			$report,
			8,
			RequireCombinedAssignmentOperatorSniff::CODE_REQUIRED_COMBINED_ASSIGMENT_OPERATOR,
			'Use "&=" operator instead of "=" and "&".'
		);
		self::assertSniffError(
			$report,
			9,
			RequireCombinedAssignmentOperatorSniff::CODE_REQUIRED_COMBINED_ASSIGMENT_OPERATOR,
			'Use "|=" operator instead of "=" and "|".'
		);
		self::assertSniffError(
			$report,
			10,
			RequireCombinedAssignmentOperatorSniff::CODE_REQUIRED_COMBINED_ASSIGMENT_OPERATOR,
			'Use ".=" operator instead of "=" and ".".'
		);
		self::assertSniffError(
			$report,
			11,
			RequireCombinedAssignmentOperatorSniff::CODE_REQUIRED_COMBINED_ASSIGMENT_OPERATOR,
			'Use "/=" operator instead of "=" and "/".'
		);
		self::assertSniffError(
			$report,
			12,
			RequireCombinedAssignmentOperatorSniff::CODE_REQUIRED_COMBINED_ASSIGMENT_OPERATOR,
			'Use "-=" operator instead of "=" and "-".'
		);
		self::assertSniffError(
			$report,
			13,
			RequireCombinedAssignmentOperatorSniff::CODE_REQUIRED_COMBINED_ASSIGMENT_OPERATOR,
			'Use "**=" operator instead of "=" and "**".'
		);
		self::assertSniffError(
			$report,
			14,
			RequireCombinedAssignmentOperatorSniff::CODE_REQUIRED_COMBINED_ASSIGMENT_OPERATOR,
			'Use "%=" operator instead of "=" and "%".'
		);
		self::assertSniffError(
			$report,
			15,
			RequireCombinedAssignmentOperatorSniff::CODE_REQUIRED_COMBINED_ASSIGMENT_OPERATOR,
			'Use "*=" operator instead of "=" and "*".'
		);
		self::assertSniffError(
			$report,
			16,
			RequireCombinedAssignmentOperatorSniff::CODE_REQUIRED_COMBINED_ASSIGMENT_OPERATOR,
			'Use "+=" operator instead of "=" and "+".'
		);
		self::assertSniffError(
			$report,
			17,
			RequireCombinedAssignmentOperatorSniff::CODE_REQUIRED_COMBINED_ASSIGMENT_OPERATOR,
			'Use "<<=" operator instead of "=" and "<<".'
		);
		self::assertSniffError(
			$report,
			18,
			RequireCombinedAssignmentOperatorSniff::CODE_REQUIRED_COMBINED_ASSIGMENT_OPERATOR,
			'Use ">>=" operator instead of "=" and ">>".'
		);
		self::assertSniffError(
			$report,
			19,
			RequireCombinedAssignmentOperatorSniff::CODE_REQUIRED_COMBINED_ASSIGMENT_OPERATOR,
			'Use "^=" operator instead of "=" and "^".'
		);

		self::assertSniffError($report, 20, RequireCombinedAssignmentOperatorSniff::CODE_REQUIRED_COMBINED_ASSIGMENT_OPERATOR);
		self::assertSniffError($report, 21, RequireCombinedAssignmentOperatorSniff::CODE_REQUIRED_COMBINED_ASSIGMENT_OPERATOR);
		self::assertSniffError($report, 24, RequireCombinedAssignmentOperatorSniff::CODE_REQUIRED_COMBINED_ASSIGMENT_OPERATOR);

		self::assertAllFixedInFile($report);
	}

}
