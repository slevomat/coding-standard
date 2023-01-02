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
				'AppAssert*',
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
				'AppAssert*',
			],
		]);

		self::assertSame(3, $report->getErrorCount());

		self::assertSniffError($report, 3, AttributesOrderSniff::CODE_INCORRECT_ORDER);
		self::assertSniffError($report, 14, AttributesOrderSniff::CODE_INCORRECT_ORDER);
		self::assertSniffError($report, 19, AttributesOrderSniff::CODE_INCORRECT_ORDER);

		self::assertAllFixedInFile($report);
	}

	public function testOrderAlphabeticallyNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/attributesOrderAlphabeticallyNoErrors.php', [
			'orderAlphabetically' => true,
		]);

		self::assertNoSniffErrorInFile($report);
	}

	public function testOrderAlphabeticallyErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/attributesOrderAlphabeticallyErrors.php', [
			'orderAlphabetically' => true,
		]);

		self::assertSame(3, $report->getErrorCount());

		self::assertSniffError($report, 3, AttributesOrderSniff::CODE_INCORRECT_ORDER);
		self::assertSniffError($report, 12, AttributesOrderSniff::CODE_INCORRECT_ORDER);
		self::assertSniffError($report, 18, AttributesOrderSniff::CODE_INCORRECT_ORDER);

		self::assertAllFixedInFile($report);
	}

	public function testNetherOrderIsSet(): void
	{
		$this->expectException(UnexpectedValueException::class);
		$this->expectExceptionMessage('Neither manual or alphabetical order is set.');

		self::checkFile(__DIR__ . '/data/attributesOrderNoErrors.php');
	}

	public function testBothOrdersAreSet(): void
	{
		$this->expectException(UnexpectedValueException::class);
		$this->expectExceptionMessage('Only one order can be set.');

		self::checkFile(__DIR__ . '/data/attributesOrderNoErrors.php', [
			'order' => [
				'SomeAttribute',
			],
			'orderAlphabetically' => true,
		]);
	}

	public function testInvalidAttributePriorPhp80(): void
	{
		$report = self::checkFile(__DIR__ . '/data/attributesOrderInvalidAttribute.php');
		self::assertNoSniffErrorInFile($report);
	}

}
