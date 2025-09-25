<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

use SlevomatCodingStandard\Sniffs\TestCase;

class ClassStructureSniffTest extends TestCase
{

	private const DIFFERENT_RULES = [
		'uses',
		'public constants, protected constants, private constants',
		'public static properties, protected static properties, private static properties',
		'public static abstract methods, public static methods, protected static abstract methods, protected static methods, private static methods',
		'public properties, protected properties, private properties',
		'enum cases',
		'magic methods',
		'public abstract methods, public methods, protected abstract methods, protected methods, private methods',
		'constructor, destructor',
		'static constructors',
		'invoke method',
		'methods',
		'public final methods',
		'public static final methods',
		'protected final methods',
		'protected static final methods',
	];

	private const METHOD_GROUPS = [
		'inject method' => 'inject',
		'inject methods' => 'inject*',
		'phpunit before class' => 'setUpBeforeClass, @beforeClass, #PHPUnit\Framework\Attributes\BeforeClass',
		'phpunit after class' => 'tearDownAfterClass, @afterClass, #PHPUnit\Framework\Attributes\AfterClass',
		'phpunit before' => 'setUp, @before, #PHPUnit\Framework\Attributes\Before',
		'phpunit after' => 'tearDown, @after, #PHPUnit\Framework\Attributes\After',
		'phpunit data provider' => '*DataProvider',
	];

	private const METHOD_GROUP_RULES = [
		'uses',
		'public constants',
		'protected constants',
		'private constants',
		'enum cases',
		'public static properties',
		'protected static properties',
		'private static properties',
		'public properties',
		'protected properties',
		'private properties',
		'constructor',
		'static constructors',
		'destructor',
		'inject method',
		'inject methods',
		'phpunit before class',
		'phpunit after class',
		'phpunit before',
		'phpunit after',
		'public methods, public final methods',
		'public abstract methods',
		'public static methods, public static final methods',
		'public static abstract methods',
		'magic methods',
		'protected methods, protected final methods',
		'protected abstract methods',
		'protected static methods, protected static final methods',
		'protected static abstract methods',
		'private methods',
		'private static methods',
		'phpunit data provider',
	];

	public function testNoErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/classStructureNoErrors.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/classStructureErrors.php');

		self::assertSame(30, $report->getErrorCount());

		self::assertSniffError($report, 6, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($report, 12, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($report, 18, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($report, 24, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($report, 33, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($report, 44, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($report, 55, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($report, 66, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($report, 77, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($report, 88, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($report, 99, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($report, 111, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($report, 117, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($report, 128, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($report, 132, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($report, 145, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($report, 154, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($report, 158, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($report, 161, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($report, 175, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($report, 188, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($report, 207, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($report, 209, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($report, 226, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($report, 234, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($report, 242, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($report, 250, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($report, 252, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);

		self::assertAllFixedInFile($report);
	}

	public function testManyErrors(): void
	{
		$report = self::checkFile(__DIR__ . '/data/classStructureManyErrors.php');

		self::assertSame(1, $report->getErrorCount());
		self::assertAllFixedInFile($report);
	}

	public function testNoErrorsWithDifferentRules(): void
	{
		$report = self::checkFile(
			__DIR__ . '/data/classStructureNoErrorsWithDifferentRules.php',
			['groups' => self::DIFFERENT_RULES],
		);

		self::assertNoSniffErrorInFile($report);
	}

	public function testErrorsWithDifferentRules(): void
	{
		$report = self::checkFile(
			__DIR__ . '/data/classStructureErrorsWithDifferentRules.php',
			['groups' => self::DIFFERENT_RULES],
		);

		self::assertSame(17, $report->getErrorCount());

		self::assertSniffError($report, 6, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($report, 12, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($report, 27, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($report, 38, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($report, 49, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($report, 60, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($report, 69, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($report, 73, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($report, 85, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($report, 94, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($report, 103, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($report, 107, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($report, 107, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($report, 107, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($report, 114, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($report, 118, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($report, 122, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($report, 126, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);

		self::assertAllFixedInFile($report);
	}

	public function testErrorsWithShortcuts(): void
	{
		$report = self::checkFile(
			__DIR__ . '/data/classStructureWithShortcutsErrors.php',
			[
				'groups' => [
					'uses',
					'enum cases',
					'constants',
					'private properties',
					'static properties',
					'properties',
					'constructor',
					'all public methods',
					'final methods',
					'public abstract methods',
					'abstract methods',
					'public static methods',
					'static methods',
					'all private methods',
					'methods',
					'magic methods',
				],
			],
		);

		self::assertSame(10, $report->getErrorCount());
		self::assertAllFixedInFile($report);
	}

	public function testNoErrorsWithMethodGroupRules(): void
	{
		$report = self::checkFile(
			__DIR__ . '/data/classStructureNoErrorsWithMethodGroupRules.php',
			[
				'methodGroups' => self::METHOD_GROUPS,
				'groups' => self::METHOD_GROUP_RULES,
			],
		);

		self::assertNoSniffErrorInFile($report);
	}

	public function testErrorsWithMethodGroupRules(): void
	{
		$report = self::checkFile(
			__DIR__ . '/data/classStructureErrorsWithMethodGroupRules.php',
			[
				'methodGroups' => self::METHOD_GROUPS,
				'groups' => self::METHOD_GROUP_RULES,
			],
		);

		self::assertSame(7, $report->getErrorCount());
		self::assertSniffError($report, 22, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($report, 33, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($report, 44, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($report, 48, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($report, 72, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($report, 76, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertSniffError($report, 80, ClassStructureSniff::CODE_INCORRECT_GROUP_ORDER);
		self::assertAllFixedInFile($report);
	}

	public function testThrowExceptionForUnsupportedGroup(): void
	{
		try {
			self::checkFile(
				__DIR__ . '/data/classStructureNoErrors.php',
				['groups' => ['whatever']],
			);
			self::fail();
		} catch (UnsupportedClassGroupException $e) {
			self::assertStringContainsString('whatever', $e->getMessage());
		}
	}

	public function testThrowExceptionForMissingGroups(): void
	{
		try {
			self::checkFile(
				__DIR__ . '/data/classStructureNoErrors.php',
				['groups' => ['uses']],
			);
			self::fail();
		} catch (MissingClassGroupsException $e) {
			self::assertStringContainsString(', constructor, static constructors, destructor, ', $e->getMessage());
		}
	}

}
