<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

class SuppressHelperTest extends \SlevomatCodingStandard\Helpers\TestCase
{

	const CHECK_NAME = 'Sniff.Sniff.Sniff.check';

	/** @var \PHP_CodeSniffer\Files\File */
	private $testedCodeSnifferFile;

	public function testClassIsSuppressed()
	{
		$this->assertTrue(SuppressHelper::isSniffSuppressed($this->getTestedCodeSnifferFile(), $this->findClassPointerByName($this->getTestedCodeSnifferFile(), 'IsSuppressed'), self::CHECK_NAME));
	}

	public function testClassIsNotSuppressed()
	{
		$this->assertFalse(SuppressHelper::isSniffSuppressed($this->getTestedCodeSnifferFile(), $this->findClassPointerByName($this->getTestedCodeSnifferFile(), 'IsNotSuppressed'), self::CHECK_NAME));
	}

	public function testConstantIsSuppressed()
	{
		$this->assertTrue(SuppressHelper::isSniffSuppressed($this->getTestedCodeSnifferFile(), $this->findConstantPointerByName($this->getTestedCodeSnifferFile(), 'IS_SUPPRESSED'), self::CHECK_NAME));
	}

	public function testConstantIsNotSuppressed()
	{
		$this->assertFalse(SuppressHelper::isSniffSuppressed($this->getTestedCodeSnifferFile(), $this->findConstantPointerByName($this->getTestedCodeSnifferFile(), 'IS_NOT_SUPPRESSED'), self::CHECK_NAME));
	}

	public function testPropertyIsSuppressed()
	{
		$this->assertTrue(SuppressHelper::isSniffSuppressed($this->getTestedCodeSnifferFile(), $this->findPropertyPointerByName($this->getTestedCodeSnifferFile(), 'isSuppressed'), self::CHECK_NAME));
	}

	public function testPropertyIsNotSuppressed()
	{
		$this->assertFalse(SuppressHelper::isSniffSuppressed($this->getTestedCodeSnifferFile(), $this->findPropertyPointerByName($this->getTestedCodeSnifferFile(), 'isNotSuppressed'), self::CHECK_NAME));
	}

	/**
	 * @return string[][]
	 */
	public function dataFunctionIsSuppressed(): array
	{
		return [
			['suppressWithFullName'],
			['suppressWithPartialName'],
			['suppressWithFullDocComment'],
		];
	}

	/**
	 * @dataProvider dataFunctionIsSuppressed
	 * @param string $name
	 */
	public function testFunctionIsSuppressed(string $name)
	{
		$this->assertTrue(SuppressHelper::isSniffSuppressed($this->getTestedCodeSnifferFile(), $this->findFunctionPointerByName($this->getTestedCodeSnifferFile(), $name), self::CHECK_NAME));
	}

	/**
	 * @return string[][]
	 */
	public function dataFunctionIsNotSuppressed(): array
	{
		return [
			['noDocComment'],
			['docCommentWithoutSuppress'],
			['invalidSuppress'],
		];
	}

	/**
	 * @dataProvider dataFunctionIsNotSuppressed
	 * @param string $name
	 */
	public function testFunctionIsNotSuppressed(string $name)
	{
		$this->assertFalse(SuppressHelper::isSniffSuppressed($this->getTestedCodeSnifferFile(), $this->findFunctionPointerByName($this->getTestedCodeSnifferFile(), $name), self::CHECK_NAME));
	}

	private function getTestedCodeSnifferFile(): \PHP_CodeSniffer\Files\File
	{
		if ($this->testedCodeSnifferFile === null) {
			$this->testedCodeSnifferFile = $this->getCodeSnifferFile(
				__DIR__ . '/data/suppress.php'
			);
		}
		return $this->testedCodeSnifferFile;
	}

}
