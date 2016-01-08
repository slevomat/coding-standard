<?php

namespace SlevomatCodingStandard\Sniffs\Typehints;

use PHP_CodeSniffer_File;
use SlevomatCodingStandard\Helpers\TokenHelper;

class DeclareStrictTypesSniff implements \PHP_CodeSniffer_Sniff
{

	const CODE_DECLARE_STRICT_TYPES_MISSING = 'declareStrictTypesMissing';

	const CODE_INCORRECT_WHITESPACE_BETWEEN_OPEN_TAG_AND_DECLARE = 'incorrectWhitespaceBetweenOpenTagAndDeclare';

	public $newlinesCountBetweenOpenTagAndDeclare = 0;

	/** @var string[] */
	private static $alreadyProcessedFiles = [];

	/**
	 * @return integer[]
	 */
	public function register()
	{
		return [
			T_OPEN_TAG,
		];
	}

	/**
	 * @param \PHP_CodeSniffer_File $phpcsFile
	 * @param integer $openTagPointer
	 */
	public function process(PHP_CodeSniffer_File $phpcsFile, $openTagPointer)
	{
		if (isset(self::$alreadyProcessedFiles[$phpcsFile->getFilename()])) {
			return;
		}

		self::$alreadyProcessedFiles[$phpcsFile->getFilename()] = true;

		$tokens = $phpcsFile->getTokens();
		$declarePointer = TokenHelper::findNextNonWhitespace($phpcsFile, $openTagPointer + 1);
		if ($tokens[$declarePointer]['code'] !== T_DECLARE) {
			$this->reportMissingDeclareStrict($phpcsFile, $openTagPointer);
			return;
		}
		$stringPointer = $phpcsFile->findNext(T_STRING, $declarePointer + 1);
		if ($tokens[$stringPointer]['content'] !== 'strict_types') {
			$this->reportMissingDeclareStrict($phpcsFile, $declarePointer);
			return;
		}

		$numberPointer = $phpcsFile->findNext(T_LNUMBER, $stringPointer + 1);
		if ($tokens[$numberPointer]['content'] !== '1') {
			$this->reportMissingDeclareStrict($phpcsFile, $declarePointer);
			return;
		}

		$openingWhitespace = substr($tokens[$openTagPointer]['content'], strlen('<?php'));
		$newlinesCountBetweenOpenTagAndDeclare = (int) trim($this->newlinesCountBetweenOpenTagAndDeclare);
		if ($newlinesCountBetweenOpenTagAndDeclare === 0) {
			if ($openingWhitespace !== ' ') {
				$phpcsFile->addError(
					'There must be a single space between the PHP open tag and declare statement.',
					$declarePointer,
					self::CODE_INCORRECT_WHITESPACE_BETWEEN_OPEN_TAG_AND_DECLARE
				);
			}
		} else {
			$startToken = $openTagPointer + 1;
			do {
				$possibleWhitespacePointer = TokenHelper::findNextAnyToken($phpcsFile, $startToken);
				if ($possibleWhitespacePointer !== null && $tokens[$possibleWhitespacePointer]['code'] === T_WHITESPACE) {
					$openingWhitespace .= $tokens[$possibleWhitespacePointer]['content'];
				}
				$startToken++;
			} while ($possibleWhitespacePointer !== null && $tokens[$possibleWhitespacePointer]['code'] === T_WHITESPACE);
			$newlinesCount = substr_count($openingWhitespace, $phpcsFile->eolChar);
			if ($newlinesCount !== $newlinesCountBetweenOpenTagAndDeclare) {
				$phpcsFile->addError(
					sprintf(
						'Expected %d newlines between PHP open tag and declare statement, found %d.',
						$newlinesCountBetweenOpenTagAndDeclare,
						$newlinesCount
					),
					$declarePointer,
					self::CODE_INCORRECT_WHITESPACE_BETWEEN_OPEN_TAG_AND_DECLARE
				);
			}
		}
	}

	/**
	 * @param \PHP_CodeSniffer_File $phpcsFile
	 * @param integer $openTagPointer
	 */
	private function reportMissingDeclareStrict(PHP_CodeSniffer_File $phpcsFile, $openTagPointer)
	{
		$phpcsFile->addError(
			'Missing declare(strict_types=1).',
			$openTagPointer,
			self::CODE_DECLARE_STRICT_TYPES_MISSING
		);
	}

}
