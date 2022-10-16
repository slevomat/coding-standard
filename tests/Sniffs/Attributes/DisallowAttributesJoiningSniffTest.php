<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Attributes;

use SlevomatCodingStandard\Sniffs\TestCase;

class DisallowAttributesJoiningSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/disallowedAttributesJoiningNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testInvalidAttributePriorPhp80NoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/disallowedAttributesJoiningInvalidAttribute.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/disallowedAttributesJoiningErrors.php');

		self::assertSame(12, $report->getErrorCount());

		self::assertSniffError($report, 3, DisallowAttributesJoiningSniff::CODE_DISALLOWED_ATTRIBUTES_JOINING);
		self::assertSniffError($report, 7, DisallowAttributesJoiningSniff::CODE_DISALLOWED_ATTRIBUTES_JOINING);
		self::assertSniffError($report, 11, DisallowAttributesJoiningSniff::CODE_DISALLOWED_ATTRIBUTES_JOINING);
		self::assertSniffError($report, 14, DisallowAttributesJoiningSniff::CODE_DISALLOWED_ATTRIBUTES_JOINING);
		self::assertSniffError($report, 20, DisallowAttributesJoiningSniff::CODE_DISALLOWED_ATTRIBUTES_JOINING);
		self::assertSniffError($report, 23, DisallowAttributesJoiningSniff::CODE_DISALLOWED_ATTRIBUTES_JOINING);
		self::assertSniffError($report, 29, DisallowAttributesJoiningSniff::CODE_DISALLOWED_ATTRIBUTES_JOINING);
		self::assertSniffError($report, 34, DisallowAttributesJoiningSniff::CODE_DISALLOWED_ATTRIBUTES_JOINING);
		self::assertSniffError($report, 37, DisallowAttributesJoiningSniff::CODE_DISALLOWED_ATTRIBUTES_JOINING);
		self::assertSniffError($report, 43, DisallowAttributesJoiningSniff::CODE_DISALLOWED_ATTRIBUTES_JOINING);
		self::assertSniffError($report, 49, DisallowAttributesJoiningSniff::CODE_DISALLOWED_ATTRIBUTES_JOINING);
		self::assertSniffError($report, 52, DisallowAttributesJoiningSniff::CODE_DISALLOWED_ATTRIBUTES_JOINING);

		self::assertAllFixedInFile($report);
	}

}
