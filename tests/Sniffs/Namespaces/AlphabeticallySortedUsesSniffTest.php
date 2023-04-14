<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

use SlevomatCodingStandard\Sniffs\TestCase;

class AlphabeticallySortedUsesSniffTest extends TestCase
{

	public function testIncorrectOrder(): void
	{
		self::assertSniffError(
			self::checkFile(__DIR__ . '/data/incorrectOrder.php'),
			5,
			AlphabeticallySortedUsesSniff::CODE_INCORRECT_ORDER,
			'Second\FooObject'
		);
	}

	public function testIncorrectOrderIntertwinedWithClasses(): void
	{
		self::assertSniffError(
			self::checkFile(__DIR__ . '/data/incorrectOrderIntertwinedWithClasses.php'),
			18,
			AlphabeticallySortedUsesSniff::CODE_INCORRECT_ORDER,
			'Delta'
		);
	}

	public function testCorrectOrderIgnoresUsesAfterClassesTraitsAndInterfaces(): void
	{
		self::assertNoSniffErrorInFile(
			self::checkFile(__DIR__ . '/data/correctOrder.php')
		);
	}

	public function testCorrectOrderOfSimilarNamespaces(): void
	{
		self::assertNoSniffErrorInFile(
			self::checkFile(__DIR__ . '/data/correctOrderSimilarNamespaces.php')
		);
	}

	public function testCorrectOrderOfSimilarNamespacesCaseSensitive(): void
	{
		self::assertNoSniffErrorInFile(
			self::checkFile(__DIR__ . '/data/correctOrderSimilarNamespacesCaseSensitive.php', [
				'caseSensitive' => true,
			])
		);
	}

	public function testIncorrectOrderOfSimilarNamespaces(): void
	{
		self::assertSniffError(
			self::checkFile(__DIR__ . '/data/incorrectOrderSimilarNamespaces.php'),
			6,
			AlphabeticallySortedUsesSniff::CODE_INCORRECT_ORDER,
			'Foo\Bar'
		);
	}

	public function testPatrikOrder(): void
	{
		$report = self::checkFile(__DIR__ . '/data/alphabeticalPatrik.php');
		self::assertNoSniffErrorInFile($report);
	}

	public function testIncorrectOrderNotFixable(): void
	{
		$report = self::checkFile(
			__DIR__ . '/data/alphabeticalMultiPartUseNotFixable.php',
			[],
			[AlphabeticallySortedUsesSniff::CODE_INCORRECT_ORDER]
		);

		self::assertSniffError($report, 6, AlphabeticallySortedUsesSniff::CODE_INCORRECT_ORDER, 'B');

		self::assertAllFixedInFile($report);
	}

	public function testIncorrectOrderGroupUseNotFixable(): void
	{
		$report = self::checkFile(
			__DIR__ . '/data/alphabeticalGroupUseNotFixable.php',
			[],
			[AlphabeticallySortedUsesSniff::CODE_INCORRECT_ORDER]
		);

		self::assertNoSniffErrorInFile($report);

		self::assertAllFixedInFile($report);
	}

	public function testFixable(): void
	{
		$report = self::checkFile(
			__DIR__ . '/data/fixableAlphabeticalSortedUses.php',
			[],
			[AlphabeticallySortedUsesSniff::CODE_INCORRECT_ORDER]
		);
		self::assertAllFixedInFile($report);
	}

	public function testFixableWithCommentBeforeFirst(): void
	{
		$report = self::checkFile(
			__DIR__ . '/data/fixableAlphabeticalSortedUsesWithCommentBeforeFirst.php',
			[],
			[AlphabeticallySortedUsesSniff::CODE_INCORRECT_ORDER]
		);
		self::assertAllFixedInFile($report);
	}

	public function testFixableWithDocComment(): void
	{
		$report = self::checkFile(
			__DIR__ . '/data/fixableAlphabeticalSortedUsesWithDocComment.php',
			[],
			[AlphabeticallySortedUsesSniff::CODE_INCORRECT_ORDER]
		);
		self::assertAllFixedInFile($report);
	}

	public function testFixableNotPsr12Compatible(): void
	{
		$report = self::checkFile(__DIR__ . '/data/fixableAlphabeticalSortedUsesNotPsr12Compatible.php', [
			'psr12Compatible' => false,
		], [AlphabeticallySortedUsesSniff::CODE_INCORRECT_ORDER]);
		self::assertAllFixedInFile($report);
	}

}
