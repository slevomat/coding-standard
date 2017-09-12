<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

class AlphabeticallySortedUsesSniffTest extends \SlevomatCodingStandard\Sniffs\TestCase
{

	public function testIncorrectOrder(): void
	{
		$this->assertSniffError(
			$this->checkFile(__DIR__ . '/data/incorrectOrder.php'),
			5,
			AlphabeticallySortedUsesSniff::CODE_INCORRECT_ORDER,
			'Second\FooObject'
		);
	}

	public function testIncorrectOrderIntertwinedWithClasses(): void
	{
		$this->assertSniffError(
			$this->checkFile(__DIR__ . '/data/incorrectOrderIntertwinedWithClasses.php'),
			18,
			AlphabeticallySortedUsesSniff::CODE_INCORRECT_ORDER,
			'Delta'
		);
	}

	public function testCorrectOrderIgnoresUsesAfterClassesTraitsAndInterfaces(): void
	{
		$this->assertNoSniffErrorInFile(
			$this->checkFile(__DIR__ . '/data/correctOrder.php')
		);
	}

	public function testCorrectOrderOfSimilarNamespaces(): void
	{
		$this->assertNoSniffErrorInFile(
			$this->checkFile(__DIR__ . '/data/correctOrderSimilarNamespaces.php')
		);
	}

	public function testCorrectOrderOfSimilarNamespacesCaseSensitive(): void
	{
		$this->assertNoSniffErrorInFile(
			$this->checkFile(__DIR__ . '/data/correctOrderSimilarNamespacesCaseSensitive.php', [
				'caseSensitive' => true,
			])
		);
	}

	public function testIncorrectOrderOfSimilarNamespaces(): void
	{
		$this->assertSniffError(
			$this->checkFile(__DIR__ . '/data/incorrectOrderSimilarNamespaces.php'),
			6,
			AlphabeticallySortedUsesSniff::CODE_INCORRECT_ORDER,
			'Foo\Bar'
		);
	}

	public function testPatrikOrder(): void
	{
		$this->assertNoSniffErrorInFile($this->checkFile(__DIR__ . '/data/alphabeticalPatrik.php'));
	}

	public function testFixableUnusedUses(): void
	{
		$report = $this->checkFile(__DIR__ . '/data/fixableAlphabeticalSortedUses.php', [], [AlphabeticallySortedUsesSniff::CODE_INCORRECT_ORDER]);
		$this->assertAllFixedInFile($report);
	}

}
