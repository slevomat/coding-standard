<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

class AlphabeticallySortedUsesSniffTest extends \SlevomatCodingStandard\Sniffs\TestCase
{

	public function testIncorrectOrder()
	{
		$this->assertSniffError(
			$this->checkFile(__DIR__ . '/data/incorrectOrder.php'),
			5,
			AlphabeticallySortedUsesSniff::CODE_INCORRECT_ORDER,
			'Second\FooObject'
		);
	}

	public function testIncorrectOrderIntertwinedWithClasses()
	{
		$this->assertSniffError(
			$this->checkFile(__DIR__ . '/data/incorrectOrderIntertwinedWithClasses.php'),
			18,
			AlphabeticallySortedUsesSniff::CODE_INCORRECT_ORDER,
			'Delta'
		);
	}

	public function testCorrectOrderIgnoresUsesAfterClassesTraitsAndInterfaces()
	{
		$this->assertNoSniffErrorInFile(
			$this->checkFile(__DIR__ . '/data/correctOrder.php')
		);
	}

	public function testCorrectOrderOfSimilarNamespaces()
	{
		$this->assertNoSniffErrorInFile(
			$this->checkFile(__DIR__ . '/data/correctOrderSimilarNamespaces.php')
		);
	}

	public function testIncorrectOrderOfSimilarNamespaces()
	{
		$this->assertSniffError(
			$this->checkFile(__DIR__ . '/data/incorrectOrderSimilarNamespaces.php'),
			6,
			AlphabeticallySortedUsesSniff::CODE_INCORRECT_ORDER,
			'Foo\Bar'
		);
	}

	public function testPatrikOrder()
	{
		$this->assertNoSniffErrorInFile($this->checkFile(__DIR__ . '/data/alphabeticalPatrik.php'));
	}

}
