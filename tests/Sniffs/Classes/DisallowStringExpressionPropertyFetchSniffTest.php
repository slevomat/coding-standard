<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

use SlevomatCodingStandard\Sniffs\TestCase;

class DisallowStringExpressionPropertyFetchSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/disallowStringExpressionPropertyFetchNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/disallowStringExpressionPropertyFetchErrors.php');

		self::assertSame(3, $report->getErrorCount());

		self::assertSniffError($report, 3, DisallowStringExpressionPropertyFetchSniff::CODE_DISALLOWED_STRING_EXPRESSION_PROPERTY_FETCH);
		self::assertSniffError($report, 5, DisallowStringExpressionPropertyFetchSniff::CODE_DISALLOWED_STRING_EXPRESSION_PROPERTY_FETCH);
		self::assertSniffError($report, 7, DisallowStringExpressionPropertyFetchSniff::CODE_DISALLOWED_STRING_EXPRESSION_PROPERTY_FETCH);

		self::assertAllFixedInFile($report);
	}

}
