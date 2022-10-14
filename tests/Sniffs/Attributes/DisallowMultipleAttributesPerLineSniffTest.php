<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Attributes;

use SlevomatCodingStandard\Sniffs\TestCase;

class DisallowMultipleAttributesPerLineSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/disallowedMultipleAttributesPerLineNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/disallowedMultipleAttributesPerLineErrors.php');

		self::assertSame(17, $report->getErrorCount());

		self::assertSniffError($report, 3, DisallowMultipleAttributesPerLineSniff::CODE_DISALLOWED_MULTIPLE_ATTRIBUTES_PER_LINE);
		self::assertSniffError($report, 4, DisallowMultipleAttributesPerLineSniff::CODE_DISALLOWED_MULTIPLE_ATTRIBUTES_PER_LINE);
		self::assertSniffError($report, 7, DisallowMultipleAttributesPerLineSniff::CODE_DISALLOWED_MULTIPLE_ATTRIBUTES_PER_LINE);
		self::assertSniffError($report, 11, DisallowMultipleAttributesPerLineSniff::CODE_DISALLOWED_MULTIPLE_ATTRIBUTES_PER_LINE);
		self::assertSniffError($report, 14, DisallowMultipleAttributesPerLineSniff::CODE_DISALLOWED_MULTIPLE_ATTRIBUTES_PER_LINE);
		self::assertSniffError($report, 20, DisallowMultipleAttributesPerLineSniff::CODE_DISALLOWED_MULTIPLE_ATTRIBUTES_PER_LINE);
		self::assertSniffError($report, 23, DisallowMultipleAttributesPerLineSniff::CODE_DISALLOWED_MULTIPLE_ATTRIBUTES_PER_LINE);
		self::assertSniffError($report, 29, DisallowMultipleAttributesPerLineSniff::CODE_DISALLOWED_MULTIPLE_ATTRIBUTES_PER_LINE);
		self::assertSniffError($report, 32, DisallowMultipleAttributesPerLineSniff::CODE_DISALLOWED_MULTIPLE_ATTRIBUTES_PER_LINE);
		self::assertSniffError($report, 39, DisallowMultipleAttributesPerLineSniff::CODE_DISALLOWED_MULTIPLE_ATTRIBUTES_PER_LINE);
		self::assertSniffError($report, 40, DisallowMultipleAttributesPerLineSniff::CODE_DISALLOWED_MULTIPLE_ATTRIBUTES_PER_LINE);
		self::assertSniffError($report, 45, DisallowMultipleAttributesPerLineSniff::CODE_DISALLOWED_MULTIPLE_ATTRIBUTES_PER_LINE);
		self::assertSniffError($report, 48, DisallowMultipleAttributesPerLineSniff::CODE_DISALLOWED_MULTIPLE_ATTRIBUTES_PER_LINE);
		self::assertSniffError($report, 54, DisallowMultipleAttributesPerLineSniff::CODE_DISALLOWED_MULTIPLE_ATTRIBUTES_PER_LINE);
		self::assertSniffError($report, 60, DisallowMultipleAttributesPerLineSniff::CODE_DISALLOWED_MULTIPLE_ATTRIBUTES_PER_LINE);
		self::assertSniffError($report, 63, DisallowMultipleAttributesPerLineSniff::CODE_DISALLOWED_MULTIPLE_ATTRIBUTES_PER_LINE);

		self::assertAllFixedInFile($report);
	}

	public function testInvalidAttributePriorPhp80(): void
	{
		$report = self::checkFile(__DIR__ . '/data/disallowedMultipleAttributesPerLinesInvalidAttribute.php');
		self::assertNoSniffErrorInFile($report);
	}

}
