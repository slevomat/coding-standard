<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

use SlevomatCodingStandard\Sniffs\TestCase;

class ClassKeywordOrderSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/classKeywordOrderNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/classKeywordOrderErrors.php');

		self::assertSame(2, $report->getErrorCount());

		self::assertSniffError(
			$report,
			30,
			ClassKeywordOrderSniff::CODE_WRONG_CLASS_KEYWORD_ORDER,
			'Class keywords are not in the correct order. Found: "readonly final class"; Expected: "final readonly class"',
		);

		self::assertSniffError(
			$report,
			49,
			ClassKeywordOrderSniff::CODE_WRONG_CLASS_KEYWORD_ORDER,
			'Class keywords are not in the correct order. Found: "readonly abstract class"; Expected: "abstract readonly class"',
		);

		self::assertAllFixedInFile($report);
	}

}
