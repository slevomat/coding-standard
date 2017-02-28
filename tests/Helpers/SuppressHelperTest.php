<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

class SuppressHelperTest extends \SlevomatCodingStandard\Helpers\TestCase
{

	const CHECK_NAME = 'Sniff.Sniff.Sniff.check';

	/** @var \PHP_CodeSniffer_File */
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

	public function dataFunctionIsSuppressed()
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
	public function testFunctionIsSuppressed($name)
	{
		$this->assertTrue(SuppressHelper::isSniffSuppressed($this->getTestedCodeSnifferFile(), $this->findFunctionPointerByName($this->getTestedCodeSnifferFile(), $name), self::CHECK_NAME));
	}

	public function dataFunctionIsNotSuppressed()
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
	public function testFunctionIsNotSuppressed($name)
	{
		$this->assertFalse(SuppressHelper::isSniffSuppressed($this->getTestedCodeSnifferFile(), $this->findFunctionPointerByName($this->getTestedCodeSnifferFile(), $name), self::CHECK_NAME));
	}

	private function getTestedCodeSnifferFile()
	{
		if ($this->testedCodeSnifferFile === null) {
			$this->testedCodeSnifferFile = $this->getCodeSnifferFile(
				__DIR__ . '/data/suppress.php'
			);
		}
		return $this->testedCodeSnifferFile;
	}

}
