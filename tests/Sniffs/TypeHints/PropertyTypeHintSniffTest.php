<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\TypeHints;

use SlevomatCodingStandard\Sniffs\TestCase;

class PropertyTypeHintSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/propertyTypeHintNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/propertyTypeHintErrors.php', [
			'traversableTypeHints' => ['Traversable'],
		]);

		self::assertSame(4, $report->getErrorCount());

		self::assertSniffError($report, 9, PropertyTypeHintSniff::CODE_MISSING_ANY_TYPE_HINT);
		self::assertSniffError($report, 11, PropertyTypeHintSniff::CODE_MISSING_TRAVERSABLE_TYPE_HINT_SPECIFICATION);
		self::assertSniffError($report, 14, PropertyTypeHintSniff::CODE_MISSING_TRAVERSABLE_TYPE_HINT_SPECIFICATION);
		self::assertSniffError($report, 17, PropertyTypeHintSniff::CODE_MISSING_TRAVERSABLE_TYPE_HINT_SPECIFICATION);

		self::assertAllFixedInFile($report);
	}

}
