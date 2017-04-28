<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\TypeHints;

use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;

class DeclareStrictTypesSniff implements \PHP_CodeSniffer\Sniffs\Sniff
{

	const CODE_DECLARE_STRICT_TYPES_MISSING = 'DeclareStrictTypesMissing';

	const CODE_INCORRECT_STRICT_TYPES_FORMAT = 'IncorrectStrictTypesFormat';

	const CODE_INCORRECT_WHITESPACE_BETWEEN_OPEN_TAG_AND_DECLARE = 'IncorrectWhitespaceBetweenOpenTagAndDeclare';

	/** @var int */
	public $newlinesCountBetweenOpenTagAndDeclare = 0;

	/** @var int */
	public $spacesCountAroundEqualsSign = 1;

	/**
	 * @return int[]
	 */
	public function register(): array
	{
		return [
			T_OPEN_TAG,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $openTagPointer
	 */
	public function process(\PHP_CodeSniffer\Files\File $phpcsFile, $openTagPointer)
	{
		if ($phpcsFile->findPrevious(T_OPEN_TAG, $openTagPointer - 1) !== false) {
			return;
		}

		$tokens = $phpcsFile->getTokens();
		$declarePointer = TokenHelper::findNextEffective($phpcsFile, $openTagPointer + 1);

		if ($declarePointer === null || $tokens[$declarePointer]['code'] !== T_DECLARE) {
			$fix = $phpcsFile->addFixableError(
				'Missing declare(strict_types = 1).',
				$openTagPointer,
				self::CODE_DECLARE_STRICT_TYPES_MISSING
			);
			if ($fix) {
				$phpcsFile->fixer->beginChangeset();
				$phpcsFile->fixer->addContent($openTagPointer, sprintf('declare(strict_types = 1);%s', $phpcsFile->eolChar));
				$phpcsFile->fixer->endChangeset();
			}
			return;
		}

		$strictTypesPointer = null;
		for ($i = $tokens[$declarePointer]['parenthesis_opener'] + 1; $i < $tokens[$declarePointer]['parenthesis_closer']; $i++) {
			if ($tokens[$i]['code'] === T_STRING && $tokens[$i]['content'] === 'strict_types') {
				$strictTypesPointer = $i;
				break;
			}
		}

		if ($strictTypesPointer === null) {
			$fix = $phpcsFile->addFixableError(
				'Missing declare(strict_types = 1).',
				$declarePointer,
				self::CODE_DECLARE_STRICT_TYPES_MISSING
			);
			if ($fix) {
				$phpcsFile->fixer->beginChangeset();
				$phpcsFile->fixer->addContentBefore($tokens[$declarePointer]['parenthesis_closer'], ', strict_types = 1');
				$phpcsFile->fixer->endChangeset();
			}
			return;
		}

		$numberPointer = $phpcsFile->findNext(T_LNUMBER, $strictTypesPointer + 1);
		if ($tokens[$numberPointer]['content'] !== '1') {
			$fix = $phpcsFile->addFixableError(
				sprintf(
					'Expected strict_types = 1, found %s.',
					TokenHelper::getContent($phpcsFile, $strictTypesPointer, $numberPointer)
				),
				$declarePointer,
				self::CODE_DECLARE_STRICT_TYPES_MISSING
			);
			if ($fix) {
				$phpcsFile->fixer->beginChangeset();
				$phpcsFile->fixer->replaceToken($numberPointer, '1');
				$phpcsFile->fixer->endChangeset();
			}
			return;
		}

		$strictTypesContent = TokenHelper::getContent($phpcsFile, $strictTypesPointer, $numberPointer);
		$spacesCountAroundEqualsSign = SniffSettingsHelper::normalizeInteger($this->spacesCountAroundEqualsSign);
		$format = sprintf('strict_types%1$s=%1$s1', str_repeat(' ', $spacesCountAroundEqualsSign));
		if ($strictTypesContent !== $format) {
			$fix = $phpcsFile->addFixableError(
				sprintf(
					'Expected %s, found %s.',
					$format,
					$strictTypesContent
				),
				$strictTypesPointer,
				self::CODE_INCORRECT_STRICT_TYPES_FORMAT
			);
			if ($fix) {
				$phpcsFile->fixer->beginChangeset();
				$phpcsFile->fixer->replaceToken($strictTypesPointer, $format);
				for ($i = $strictTypesPointer + 1; $i <= $numberPointer; $i++) {
					$phpcsFile->fixer->replaceToken($i, '');
				}
				$phpcsFile->fixer->endChangeset();
			}
		}

		$openingWhitespace = substr($tokens[$openTagPointer]['content'], strlen('<?php'));
		$newlinesCountBetweenOpenTagAndDeclare = SniffSettingsHelper::normalizeInteger($this->newlinesCountBetweenOpenTagAndDeclare);
		if ($newlinesCountBetweenOpenTagAndDeclare === 0) {
			if ($openingWhitespace !== ' ') {
				$fix = $phpcsFile->addFixableError(
					'There must be a single space between the PHP open tag and declare statement.',
					$declarePointer,
					self::CODE_INCORRECT_WHITESPACE_BETWEEN_OPEN_TAG_AND_DECLARE
				);
				if ($fix) {
					$phpcsFile->fixer->beginChangeset();
					$phpcsFile->fixer->replaceToken($openTagPointer, '<?php ');
					for ($i = $openTagPointer + 1; $i < $declarePointer; $i++) {
						$phpcsFile->fixer->replaceToken($i, '');
					}
					$phpcsFile->fixer->endChangeset();
				}
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
				$fix = $phpcsFile->addFixableError(
					sprintf(
						'Expected %d newlines between PHP open tag and declare statement, found %d.',
						$newlinesCountBetweenOpenTagAndDeclare,
						$newlinesCount
					),
					$declarePointer,
					self::CODE_INCORRECT_WHITESPACE_BETWEEN_OPEN_TAG_AND_DECLARE
				);
				if ($fix) {
					$phpcsFile->fixer->beginChangeset();
					$phpcsFile->fixer->replaceToken($openTagPointer, '<?php');
					for ($i = $openTagPointer + 1; $i < $declarePointer; $i++) {
						$phpcsFile->fixer->replaceToken($i, '');
					}
					for ($i = 0; $i < $newlinesCountBetweenOpenTagAndDeclare; $i++) {
						$phpcsFile->fixer->addNewline($openTagPointer);
					}
					$phpcsFile->fixer->endChangeset();
				}
			}
		}
	}

}
