<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Attributes;

use SlevomatCodingStandard\Sniffs\TestCase;
use UnexpectedValueException;

class AttributesOrderSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/attributesOrderNoErrors.php', [
			'order' => [
				'Attribute1',
				'Attribute2',
				'Attribute3',
				'Group\\Attribute1',
				'Group\\Attribute2',
				'Group\\',
			],
		]);
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/attributesOrderErrors.php', [
			'order' => [
				'Attribute1',
				'Attribute2',
				'Attribute3',
				'Group\\Attribute1',
				'Group\\Attribute2',
				'Group\\',
			],
		]);

		self::assertSame(3, $report->getErrorCount());

		self::assertSniffError($report, 3, AttributesOrderSniff::CODE_INCORRECT_ORDER);
		self::assertSniffError($report, 12, AttributesOrderSniff::CODE_INCORRECT_ORDER);
		self::assertSniffError($report, 17, AttributesOrderSniff::CODE_INCORRECT_ORDER);

		self::assertAllFixedInFile($report);
	}

	public function testOrderNotSpecified(): void
	{
		self::expectException(UnexpectedValueException::class);
		self::checkFile(__DIR__ . '/data/attributesOrderNoErrors.php');
	}

	public function testInvalidAttributePriorPhp80(): void
	{
		$report = self::checkFile(__DIR__ . '/data/attributesOrderInvalidAttribute.php');
		self::assertNoSniffErrorInFile($report);
	}

}
