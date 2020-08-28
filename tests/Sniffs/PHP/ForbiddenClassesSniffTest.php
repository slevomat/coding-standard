<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\PHP;

use SlevomatCodingStandard\Sniffs\TestCase;

class ForbiddenClassesSniffTest extends TestCase
{

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/forbiddenClasses.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/forbiddenClasses.php', [
			'forbiddenClasses' => [
				'\\UserModel' => '\\MyApp\\Models\\User',
				'\\DB' => '\\TypeORM\\DB',
				'\\SomeClass' => 'NULL',
				'\\FullyQualified\\Nested\\SomeClass' => '\\FullyQualified\\SomeClass',
			],
			'forbiddenExtends' => [
				'\\FooNamespace\\ForbiddenExtendedClass' => '\\BarNamespace\\BaseClass',
			],
			'forbiddenInterfaces' => [
				'\\FooNamespace\\SecondImplementedInterface' => '\\BarNamespace\\SecondImplementedInterface',
				'\\SecondImplementedInterface' => null,
				'\\ThirdImplementedInterface' => '',
			],
			'forbiddenTraits' => [
				'\\SomeTraitB' => '\\TotallyDifferentTrait',
				'\\FullyQualified\\SometTotallyDifferentTrait' => '\\DatabaseTrait',
			],
		]);

		self::assertSame(19, $report->getErrorCount());
		$allErrors = $report->getErrors();

		self::assertSniffError(
			$report,
			9,
			ForbiddenClassesSniff::CODE_FORBIDDEN_INTERFACE,
			'Usage of \FooNamespace\SecondImplementedInterface interface is forbidden, use \BarNamespace\SecondImplementedInterface instead'
		);
		self::assertSniffError(
			$report,
			9,
			ForbiddenClassesSniff::CODE_FORBIDDEN_INTERFACE,
			'Usage of \ThirdImplementedInterface interface is forbidden.'
		);
		self::assertSniffError($report, 13, ForbiddenClassesSniff::CODE_FORBIDDEN_TRAIT);
		self::assertSniffError($report, 14, ForbiddenClassesSniff::CODE_FORBIDDEN_TRAIT);
		self::assertSniffError($report, 25, ForbiddenClassesSniff::CODE_FORBIDDEN_CLASS);
		self::assertSniffError($report, 29, ForbiddenClassesSniff::CODE_FORBIDDEN_PARENT_CLASS);
		self::assertSniffError($report, 29, ForbiddenClassesSniff::CODE_FORBIDDEN_INTERFACE);
		self::assertSniffError($report, 31, ForbiddenClassesSniff::CODE_FORBIDDEN_TRAIT);
		self::assertSniffError($report, 38, ForbiddenClassesSniff::CODE_FORBIDDEN_CLASS);
		self::assertSniffError($report, 40, ForbiddenClassesSniff::CODE_FORBIDDEN_CLASS);
		self::assertSniffError($report, 43, ForbiddenClassesSniff::CODE_FORBIDDEN_CLASS);
		self::assertSniffError($report, 47, ForbiddenClassesSniff::CODE_FORBIDDEN_CLASS);
		self::assertSniffError($report, 48, ForbiddenClassesSniff::CODE_FORBIDDEN_CLASS);
		self::assertCount(2, $allErrors[48]);
		self::assertSniffError(
			$report,
			49,
			ForbiddenClassesSniff::CODE_FORBIDDEN_CLASS,
			'Usage of \DB class is forbidden, use \TypeORM\DB instead'
		);
		self::assertSniffError(
			$report,
			49,
			ForbiddenClassesSniff::CODE_FORBIDDEN_CLASS,
			'Usage of \UserModel class is forbidden, use \MyApp\Models\User instead'
		);
		self::assertSniffError($report, 52, ForbiddenClassesSniff::CODE_FORBIDDEN_CLASS);
		self::assertSniffError($report, 58, ForbiddenClassesSniff::CODE_FORBIDDEN_CLASS);
		self::assertSniffError($report, 66, ForbiddenClassesSniff::CODE_FORBIDDEN_TRAIT);

		self::assertAllFixedInFile($report);
	}

}
