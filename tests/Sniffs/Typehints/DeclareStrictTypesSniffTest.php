<?php

namespace SlevomatCodingStandard\Sniffs\Typehints;

class DeclareStrictTypesSniffTest extends \SlevomatCodingStandard\Sniffs\TestCase
{

	public function testMultipleOpenTagsInFile()
	{
		$this->assertNoSniffErrorInFile($this->checkFile(__DIR__ . '/data/declareStrictTypesMultipleOpenTags.php'));
	}

	public function dataDeclareStrictTypesMissing()
	{
		return [
			[
				__DIR__ . '/data/declareStrictTypesMissing.php',
				1,
			],
			[
				__DIR__ . '/data/declareTicks.php',
				3,
			],
			[
				__DIR__ . '/data/declareStrictTypesZero.php',
				3,
			],
		];
	}

	/**
	 * @dataProvider dataDeclareStrictTypesMissing
	 * @param string $file
	 * @param integer $line
	 */
	public function testDeclareStrictTypesMissing($file, $line)
	{
		$report = $this->checkFile($file);
		$this->assertSniffError(
			$report,
			$line,
			DeclareStrictTypesSniff::CODE_DECLARE_STRICT_TYPES_MISSING
		);
	}

	public function testDeclareStrictTwoNewlines()
	{
		$file = __DIR__ . '/data/declareStrictTypesTwoNewlines.php';
		$this->assertNoSniffErrorInFile($this->checkFile($file, [
			'newlinesCountBetweenOpenTagAndDeclare' => ' 2  ',
		]));
	}

	public function testDeclareStrictTwoNewlinesError()
	{
		$report = $this->checkFile(__DIR__ . '/data/declareStrictTypesTwoNewlinesError.php');
		$this->assertSniffError(
			$report,
			3,
			DeclareStrictTypesSniff::CODE_INCORRECT_WHITESPACE_BETWEEN_OPEN_TAG_AND_DECLARE,
			'There must be a single space between the PHP open tag and declare statement.'
		);
	}

	public function testDeclareStrictOneSpaceError()
	{
		$report = $this->checkFile(__DIR__ . '/data/declareStrictTypesOneSpaceError.php', [
			'newlinesCountBetweenOpenTagAndDeclare' => '2',
		]);
		$this->assertSniffError(
			$report,
			1,
			DeclareStrictTypesSniff::CODE_INCORRECT_WHITESPACE_BETWEEN_OPEN_TAG_AND_DECLARE,
			'Expected 2 newlines between PHP open tag and declare statement, found 0.'
		);
	}

	public function testDeclareStrictOneSpace()
	{
		$this->assertNoSniffErrorInFile($this->checkFile(__DIR__ . '/data/declareStrictTypesOneSpace.php'));
	}

}
