<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

use PHP_CodeSniffer\Files\File;

class SuppressHelperTest extends TestCase
{

	private const CHECK_NAME = 'Sniff.Sniff.Sniff.check';

	/** @var File */
	private $testedCodeSnifferFile;

	public function testClassIsSuppressed(): void
	{
		self::assertTrue(
			SuppressHelper::isSniffSuppressed(
				$this->getTestedCodeSnifferFile(),
				$this->findClassPointerByName($this->getTestedCodeSnifferFile(), 'IsSuppressed'),
				self::CHECK_NAME
			)
		);
	}

	public function testClassIsNotSuppressed(): void
	{
		self::assertFalse(
			SuppressHelper::isSniffSuppressed(
				$this->getTestedCodeSnifferFile(),
				$this->findClassPointerByName($this->getTestedCodeSnifferFile(), 'IsNotSuppressed'),
				self::CHECK_NAME
			)
		);
	}

	public function testConstantIsSuppressed(): void
	{
		self::assertTrue(
			SuppressHelper::isSniffSuppressed(
				$this->getTestedCodeSnifferFile(),
				$this->findConstantPointerByName($this->getTestedCodeSnifferFile(), 'IS_SUPPRESSED'),
				self::CHECK_NAME
			)
		);
	}

	public function testConstantIsNotSuppressed(): void
	{
		self::assertFalse(
			SuppressHelper::isSniffSuppressed(
				$this->getTestedCodeSnifferFile(),
				$this->findConstantPointerByName($this->getTestedCodeSnifferFile(), 'IS_NOT_SUPPRESSED'),
				self::CHECK_NAME
			)
		);
	}

	public function testPropertyIsSuppressed(): void
	{
		self::assertTrue(
			SuppressHelper::isSniffSuppressed(
				$this->getTestedCodeSnifferFile(),
				$this->findPropertyPointerByName($this->getTestedCodeSnifferFile(), 'isSuppressed'),
				self::CHECK_NAME
			)
		);
	}

	public function testPropertyIsNotSuppressed(): void
	{
		self::assertFalse(
			SuppressHelper::isSniffSuppressed(
				$this->getTestedCodeSnifferFile(),
				$this->findPropertyPointerByName($this->getTestedCodeSnifferFile(), 'isNotSuppressed'),
				self::CHECK_NAME
			)
		);
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
	public function testFunctionIsSuppressed(string $name): void
	{
		self::assertTrue(
			SuppressHelper::isSniffSuppressed(
				$this->getTestedCodeSnifferFile(),
				$this->findFunctionPointerByName($this->getTestedCodeSnifferFile(), $name),
				self::CHECK_NAME
			)
		);
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
	public function testFunctionIsNotSuppressed(string $name): void
	{
		self::assertFalse(
			SuppressHelper::isSniffSuppressed(
				$this->getTestedCodeSnifferFile(),
				$this->findFunctionPointerByName($this->getTestedCodeSnifferFile(), $name),
				self::CHECK_NAME
			)
		);
	}

	private function getTestedCodeSnifferFile(): File
	{
		if ($this->testedCodeSnifferFile === null) {
			$this->testedCodeSnifferFile = $this->getCodeSnifferFile(__DIR__ . '/data/suppress.php');
		}
		return $this->testedCodeSnifferFile;
	}

}
