<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

use SlevomatCodingStandard\Sniffs\TestCase;
use const PHP_VERSION_ID;

class ReadonlyClassSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$this->skipIfReadonlyClassIsNotSupported();
		$report = self::checkFile(__DIR__ . '/data/readonlyClassNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$this->skipIfReadonlyClassIsNotSupported();
		$report = self::checkFile(__DIR__ . '/data/readonlyClassErrors.php');
		self::assertSame(6, $report->getErrorCount());
		self::assertSniffError(
			$report,
			16,
			ReadonlyClassSniff::CODE_PROMOTED_PROPERTY_CANNOT_BE_READONLY_IN_READONLY_CLASS,
			'Promoted property $id in readonly class cannot be declared as readonly.',
		);
		self::assertSniffError(
			$report,
			17,
			ReadonlyClassSniff::CODE_PROMOTED_PROPERTY_CANNOT_BE_READONLY_IN_READONLY_CLASS,
			'Promoted property $name in readonly class cannot be declared as readonly.',
		);
		self::assertSniffError(
			$report,
			23,
			ReadonlyClassSniff::CODE_CLASS_CAN_BE_READONLY,
			'Class FinalCandidate can be marked as readonly.',
		);
		self::assertSniffError(
			$report,
			56,
			ReadonlyClassSniff::CODE_PROMOTED_PROPERTY_CANNOT_BE_READONLY_IN_READONLY_CLASS,
			'Property $extra in readonly class cannot be declared as readonly.',
		);
		self::assertSniffError(
			$report,
			65,
			ReadonlyClassSniff::CODE_PROMOTED_PROPERTY_CANNOT_BE_READONLY_IN_READONLY_CLASS,
			'Property $extra in readonly class cannot be declared as readonly.',
		);
		self::assertSniffError(
			$report,
			79,
			ReadonlyClassSniff::CODE_CLASS_CAN_BE_READONLY,
			'Class FinalWithBodyPropertiesCandidate can be marked as readonly.',
		);
		self::assertAllFixedInFile($report);
	}

	public function testNoConstructorNoErrors(): void
	{
		$this->skipIfReadonlyClassIsNotSupported();
		$report = self::checkFile(__DIR__ . '/data/readonlyClassNoConstructorNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testModifiedSettingsErrors(): void
	{
		$this->skipIfReadonlyClassIsNotSupported();
		$report = self::checkFile(__DIR__ . '/data/readonlyClassModifiedSettingsErrors.php', [
			'allowNonFinalClasses' => true,
			'ignoreTraits' => true,
		]);
		self::assertSame(3, $report->getErrorCount());
		self::assertSniffError(
			$report,
			10,
			ReadonlyClassSniff::CODE_CLASS_CAN_BE_READONLY,
			'Class NonFinalCandidate can be marked as readonly.',
		);
		self::assertSniffError(
			$report,
			17,
			ReadonlyClassSniff::CODE_CLASS_CAN_BE_READONLY,
			'Class FinalWithTraitCandidate can be marked as readonly.',
		);
		self::assertSniffError(
			$report,
			26,
			ReadonlyClassSniff::CODE_CLASS_CAN_BE_READONLY,
			'Class NonFinalWithTraitCandidate can be marked as readonly.',
		);
		self::assertAllFixedInFile($report);
	}

	public function testFindConstructorPointerBranchesNoErrors(): void
	{
		$this->skipIfReadonlyClassIsNotSupported();
		$report = self::checkFile(__DIR__ . '/data/readonlyClassFindConstructorPointerBranchesNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testShouldNotReportIfSniffIsDisabled(): void
	{
		$this->skipIfReadonlyClassIsNotSupported();
		$report = self::checkFile(__DIR__ . '/data/readonlyClassErrors.php', [
			'enable' => false,
		]);
		self::assertNoSniffErrorInFile($report);
	}

	private function skipIfReadonlyClassIsNotSupported(): void
	{
		if (PHP_VERSION_ID < 80200) {
			self::markTestSkipped('Readonly classes are supported in PHP 8.2+ only.');
		}
	}

}
