<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

class AlphabeticallySortedUsesSniffTest extends \SlevomatCodingStandard\Sniffs\TestCase
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
		self::assertNoSniffErrorInFile(self::checkFile(__DIR__ . '/data/alphabeticalPatrik.php'));
	}

	public function testFixableUnusedUses(): void
	{
		$report = self::checkFile(__DIR__ . '/data/fixableAlphabeticalSortedUses.php', [], [AlphabeticallySortedUsesSniff::CODE_INCORRECT_ORDER]);
		self::assertAllFixedInFile($report);
	}

}
