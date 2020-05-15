<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\TypeHints;

use SlevomatCodingStandard\Sniffs\TestCase;

class PropertyTypeHintSniffTest extends TestCase
{

	public function testDisabledNativeNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/propertyTypeHintDisabledNativeNoErrors.php', [
			'enableNativeTypeHint' => false,
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testDisabledNativeErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/propertyTypeHintDisabledNativeErrors.php', [
			'enableNativeTypeHint' => false,
			'traversableTypeHints' => ['Traversable'],
		]);

		self::assertSame(5, $report->getErrorCount());

		self::assertSniffError($report, 9, PropertyTypeHintSniff::CODE_MISSING_ANY_TYPE_HINT);
		self::assertSniffError($report, 11, PropertyTypeHintSniff::CODE_MISSING_TRAVERSABLE_TYPE_HINT_SPECIFICATION);
		self::assertSniffError($report, 14, PropertyTypeHintSniff::CODE_MISSING_TRAVERSABLE_TYPE_HINT_SPECIFICATION);
		self::assertSniffError($report, 17, PropertyTypeHintSniff::CODE_MISSING_TRAVERSABLE_TYPE_HINT_SPECIFICATION);
		self::assertSniffError($report, 23, PropertyTypeHintSniff::CODE_MISSING_ANY_TYPE_HINT);

		self::assertAllFixedInFile($report);
	}

	public function testEnabledNativeNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/propertyTypeHintEnabledNativeNoErrors.php', [
			'enableNativeTypeHint' => true,
			'traversableTypeHints' => ['Traversable'],
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testEnabledNativeErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/propertyTypeHintEnabledNativeErrors.php', [
			'enableNativeTypeHint' => true,
			'traversableTypeHints' => ['Traversable'],
		]);

		self::assertSame(39, $report->getErrorCount());

		self::assertSniffError($report, 6, PropertyTypeHintSniff::CODE_MISSING_ANY_TYPE_HINT);
		self::assertSniffError($report, 11, PropertyTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 16, PropertyTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 21, PropertyTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 26, PropertyTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 31, PropertyTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 34, PropertyTypeHintSniff::CODE_USELESS_ANNOTATION);
		self::assertSniffError($report, 38, PropertyTypeHintSniff::CODE_MISSING_TRAVERSABLE_TYPE_HINT_SPECIFICATION);
		self::assertSniffError($report, 41, PropertyTypeHintSniff::CODE_MISSING_TRAVERSABLE_TYPE_HINT_SPECIFICATION);
		self::assertSniffError($report, 48, PropertyTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 51, PropertyTypeHintSniff::CODE_MISSING_TRAVERSABLE_TYPE_HINT_SPECIFICATION);
		self::assertSniffError($report, 53, PropertyTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 56, PropertyTypeHintSniff::CODE_MISSING_TRAVERSABLE_TYPE_HINT_SPECIFICATION);
		self::assertSniffError($report, 61, PropertyTypeHintSniff::CODE_MISSING_TRAVERSABLE_TYPE_HINT_SPECIFICATION);
		self::assertSniffError($report, 66, PropertyTypeHintSniff::CODE_MISSING_TRAVERSABLE_TYPE_HINT_SPECIFICATION);
		self::assertSniffError($report, 68, PropertyTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 71, PropertyTypeHintSniff::CODE_MISSING_TRAVERSABLE_TYPE_HINT_SPECIFICATION);
		self::assertSniffError($report, 73, PropertyTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 76, PropertyTypeHintSniff::CODE_MISSING_TRAVERSABLE_TYPE_HINT_SPECIFICATION);
		self::assertSniffError($report, 78, PropertyTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 83, PropertyTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 88, PropertyTypeHintSniff::CODE_USELESS_ANNOTATION);
		self::assertSniffError($report, 94, PropertyTypeHintSniff::CODE_USELESS_ANNOTATION);
		self::assertSniffError($report, 99, PropertyTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 102, PropertyTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 105, PropertyTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 108, PropertyTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 111, PropertyTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 114, PropertyTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 117, PropertyTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 122, PropertyTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);

		self::assertSniffError($report, 125, PropertyTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 128, PropertyTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 131, PropertyTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 134, PropertyTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 137, PropertyTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 140, PropertyTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);
		self::assertSniffError($report, 143, PropertyTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);

		self::assertSniffError($report, 146, PropertyTypeHintSniff::CODE_MISSING_NATIVE_TYPE_HINT);

		self::assertAllFixedInFile($report);
	}

}
