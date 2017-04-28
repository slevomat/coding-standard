<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

use SlevomatCodingStandard\Helpers\ReferencedNameHelper;
use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;

class FullyQualifiedClassNameAfterKeywordSniff implements \PHP_CodeSniffer\Sniffs\Sniff
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
	private function getKeywordsToCheck(): array
	{
		if ($this->normalizedKeywordsToCheck === null) {
			$this->normalizedKeywordsToCheck = SniffSettingsHelper::normalizeArray($this->keywordsToCheck);
		}

		return $this->normalizedKeywordsToCheck;
	}

	/**
	 * @return int[]
	 */
	public function register(): array
	{
		if (count($this->getKeywordsToCheck()) === 0) {
			return [];
		}
		return array_map(function (string $keyword) {
			if (!defined($keyword)) {
				throw new \SlevomatCodingStandard\Sniffs\Namespaces\UndefinedKeywordTokenException($keyword);
			}
			return constant($keyword);
		}, $this->getKeywordsToCheck());
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $keywordPointer
	 */
	public function process(\PHP_CodeSniffer\Files\File $phpcsFile, $keywordPointer)
	{
		$tokens = $phpcsFile->getTokens();

		$nameStartPointer = TokenHelper::findNextEffective($phpcsFile, $keywordPointer + 1);
		if (!in_array($tokens[$nameStartPointer]['code'], TokenHelper::$nameTokenCodes, true)) {
			return;
		}

		$possibleCommaPointer = $this->checkReferencedName($phpcsFile, $keywordPointer, $nameStartPointer);

		if (in_array($tokens[$keywordPointer]['code'], [T_IMPLEMENTS, T_USE], true)) {
			while (true) {
				$possibleCommaPointer = TokenHelper::findNextExcluding($phpcsFile, array_merge(TokenHelper::$nameTokenCodes, [T_WHITESPACE]), $possibleCommaPointer);
				if ($possibleCommaPointer !== null) {
					$possibleCommaToken = $tokens[$possibleCommaPointer];
					if ($possibleCommaToken['code'] === T_COMMA) {
						$nameStartPointer = TokenHelper::findNextEffective($phpcsFile, $possibleCommaPointer + 1);
						$possibleCommaPointer = $this->checkReferencedName($phpcsFile, $keywordPointer, $nameStartPointer);
						continue;
					}
				}

				break;
			}
		}
	}

	private function checkReferencedName(\PHP_CodeSniffer\Files\File $phpcsFile, int $keywordPointer, int $nameStartPointer): int
	{
		$tokens = $phpcsFile->getTokens();

		if ($tokens[$keywordPointer]['code'] === T_USE) {
			$conditions = $tokens[$keywordPointer]['conditions'];

			if (count($conditions) === 0) {
				return $nameStartPointer + 1;
			}

			$lastCondition = array_values($conditions)[count($conditions) - 1];
			if ($lastCondition === T_NAMESPACE) {
				return $nameStartPointer + 1;
			}
		}

		$nameStartToken = $tokens[$nameStartPointer];
		$endPointer = ReferencedNameHelper::getReferencedNameEndPointer($phpcsFile, $nameStartPointer);
		if ($nameStartToken['code'] !== T_NS_SEPARATOR) {
			$name = TokenHelper::getContent($phpcsFile, $nameStartPointer, $endPointer);
			$keyword = $tokens[$keywordPointer]['content'];
			$phpcsFile->addError(sprintf(
				'Type %s in %s statement should be referenced via a fully qualified name.',
				$name,
				$keyword
			), $keywordPointer, self::getErrorCode($keyword));
		}

		return $endPointer;
	}

	public static function getErrorCode(string $keyword): string
	{
		return sprintf(self::CODE_NON_FULLY_QUALIFIED, ucfirst($keyword));
	}

}
