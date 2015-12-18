<?php

namespace SlevomatCodingStandard\Sniffs\Namespaces;

use PHP_CodeSniffer_File;
use SlevomatCodingStandard\Helpers\ReferencedNameHelper;
use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;

class FullyQualifiedClassNameAfterKeywordSniff implements \PHP_CodeSniffer_Sniff
{

	const CODE_NON_FULLY_QUALIFIED = 'NonFullyQualified%s';

	/**
	 * Token types as a strings (e.g. "T_IMPLEMENTS")
	 *
	 * @var string[]
	 */
	public $keywordsToCheck = [];

	/** @var string[] */
	private $normalizedKeywordsToCheck;

	/**
	 * @return string[]
	 */
	private function getKeywordsToCheck()
	{
		if ($this->normalizedKeywordsToCheck === null) {
			$this->normalizedKeywordsToCheck = SniffSettingsHelper::normalizeArray($this->keywordsToCheck);
		}

		return $this->normalizedKeywordsToCheck;
	}

	/**
	 * @return integer[]
	 */
	public function register()
	{
		if (count($this->getKeywordsToCheck()) === 0) {
			throw new \SlevomatCodingStandard\Sniffs\Namespaces\NoKeywordsException(self::class, 'keywordsToCheck');
		}
		return array_map(function ($keyword) {
			if (!defined($keyword)) {
				throw new \SlevomatCodingStandard\Sniffs\Namespaces\UndefinedKeywordTokenException($keyword);
			}
			return constant($keyword);
		}, $this->getKeywordsToCheck());
	}

	/**
	 * @param \PHP_CodeSniffer_File $phpcsFile
	 * @param integer $keywordPointer
	 */
	public function process(PHP_CodeSniffer_File $phpcsFile, $keywordPointer)
	{
		$nameStartPointer = TokenHelper::findNextNonWhitespace($phpcsFile, $keywordPointer + 1);
		$this->checkReferencedName($phpcsFile, $keywordPointer, $nameStartPointer);

		$tokens = $phpcsFile->getTokens();
		if ($tokens[$keywordPointer]['code'] === T_IMPLEMENTS) {
			$possibleCommaPointer = $keywordPointer + 1;
			while (true) {
				$possibleCommaPointer = TokenHelper::findNextExcluding($phpcsFile, array_merge(
					TokenHelper::$nameTokenCodes,
					[T_WHITESPACE]
				), $possibleCommaPointer);
				if ($possibleCommaPointer !== null) {
					$possibleCommaToken = $tokens[$possibleCommaPointer];
					if ($possibleCommaToken['code'] === T_COMMA) {
						$nameStartPointer = TokenHelper::findNextNonWhitespace($phpcsFile, $possibleCommaPointer + 1);
						$possibleCommaPointer = $this->checkReferencedName($phpcsFile, $keywordPointer, $nameStartPointer);
						continue;
					}
				}

				break;
			}
		}
	}

	/**
	 * @param \PHP_CodeSniffer_File $phpcsFile
	 * @param integer $keywordPointer
	 * @param integer $nameStartPointer
	 * @return integer Referenced name end pointer (exclusive)
	 */
	private function checkReferencedName(PHP_CodeSniffer_File $phpcsFile, $keywordPointer, $nameStartPointer)
	{
		$tokens = $phpcsFile->getTokens();
		$nameStartToken = $tokens[$nameStartPointer];
		$endPointer = ReferencedNameHelper::findReferencedNameEndPointer($phpcsFile, $nameStartPointer);
		if ($nameStartToken['code'] !== T_NS_SEPARATOR) {
			$name = TokenHelper::getContent($phpcsFile, $nameStartPointer, $endPointer);
			$keyword = $tokens[$keywordPointer]['content'];
			$phpcsFile->addError(sprintf(
				'Type %s in %s statement should be referenced via a fully qualified name',
				$name,
				$keyword
			), $keywordPointer, self::getErrorCode($keyword));
		}

		return $endPointer;
	}

	/**
	 * @param string $keyword
	 * @return string
	 */
	public static function getErrorCode($keyword)
	{
		return sprintf(self::CODE_NON_FULLY_QUALIFIED, ucfirst($keyword));
	}

}
