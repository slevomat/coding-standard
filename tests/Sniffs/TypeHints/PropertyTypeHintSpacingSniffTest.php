<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\TypeHints;

use SlevomatCodingStandard\Sniffs\TestCase;

class PropertyTypeHintSpacingSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/propertyTypeHintSpacingNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/propertyTypeHintSpacingErrors.php');

		self::assertSame(6, $report->getErrorCount());

		self::assertSniffError($report, 6, PropertyTypeHintSpacingSniff::CODE_NO_SPACE_BETWEEN_TYPE_HINT_AND_PROPERTY);
		self::assertSniffError($report, 8, PropertyTypeHintSpacingSniff::CODE_MULTIPLE_SPACES_BETWEEN_TYPE_HINT_AND_PROPERTY);
		self::assertSniffError($report, 10, PropertyTypeHintSpacingSniff::CODE_WHITESPACE_AFTER_NULLABILITY_SYMBOL);
		self::assertSniffError($report, 12, PropertyTypeHintSpacingSniff::CODE_NO_SPACE_BEFORE_NULLABILITY_SYMBOL);
		self::assertSniffError($report, 14, PropertyTypeHintSpacingSniff::CODE_MULTIPLE_SPACES_BEFORE_NULLABILITY_SYMBOL);
		self::assertSniffError($report, 16, PropertyTypeHintSpacingSniff::CODE_MULTIPLE_SPACES_BEFORE_TYPE_HINT);

		self::assertAllFixedInFile($report);
	}

}
