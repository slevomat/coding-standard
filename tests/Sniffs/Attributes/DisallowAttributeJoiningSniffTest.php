<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Attributes;

use SlevomatCodingStandard\Sniffs\TestCase;

class DisallowAttributeJoiningSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/disallowedAttributeJoiningNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testInvalidAttributePriorPHP80NoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/disallowedAttributeJoiningInvalidAttributeNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/disallowedAttributeJoiningErrors.php');

		self::assertSame(12, $report->getErrorCount());

		self::assertSniffError($report, 3, DisallowAttributeJoiningSniff::CODE_DISALLOWED_ATTRIBUTE_JOINING);
		self::assertSniffError($report, 7, DisallowAttributeJoiningSniff::CODE_DISALLOWED_ATTRIBUTE_JOINING);
		self::assertSniffError($report, 11, DisallowAttributeJoiningSniff::CODE_DISALLOWED_ATTRIBUTE_JOINING);
		self::assertSniffError($report, 14, DisallowAttributeJoiningSniff::CODE_DISALLOWED_ATTRIBUTE_JOINING);
		self::assertSniffError($report, 20, DisallowAttributeJoiningSniff::CODE_DISALLOWED_ATTRIBUTE_JOINING);
		self::assertSniffError($report, 23, DisallowAttributeJoiningSniff::CODE_DISALLOWED_ATTRIBUTE_JOINING);
		self::assertSniffError($report, 29, DisallowAttributeJoiningSniff::CODE_DISALLOWED_ATTRIBUTE_JOINING);
		self::assertSniffError($report, 34, DisallowAttributeJoiningSniff::CODE_DISALLOWED_ATTRIBUTE_JOINING);
		self::assertSniffError($report, 37, DisallowAttributeJoiningSniff::CODE_DISALLOWED_ATTRIBUTE_JOINING);
		self::assertSniffError($report, 43, DisallowAttributeJoiningSniff::CODE_DISALLOWED_ATTRIBUTE_JOINING);
		self::assertSniffError($report, 49, DisallowAttributeJoiningSniff::CODE_DISALLOWED_ATTRIBUTE_JOINING);
		self::assertSniffError($report, 52, DisallowAttributeJoiningSniff::CODE_DISALLOWED_ATTRIBUTE_JOINING);

		self::assertAllFixedInFile($report);
	}

}
