<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\TypeHints;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\CommentHelper;
use SlevomatCodingStandard\Helpers\FixerHelper;
use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function sprintf;
use function str_repeat;
use function strlen;
use function substr;
use function substr_count;
use const T_COMMENT;
use const T_DECLARE;
use const T_LNUMBER;
use const T_OPEN_TAG;
use const T_STRING;

class DeclareStrictTypesSniff implements Sniff
{

	public const CODE_DECLARE_STRICT_TYPES_MISSING = 'DeclareStrictTypesMissing';

	public const CODE_INCORRECT_STRICT_TYPES_FORMAT = 'IncorrectStrictTypesFormat';

	public const CODE_INCORRECT_WHITESPACE_BEFORE_DECLARE = 'IncorrectWhitespaceBeforeDeclare';

	public const CODE_INCORRECT_WHITESPACE_AFTER_DECLARE = 'IncorrectWhitespaceAfterDeclare';

	/** @var bool */
	public $declareOnFirstLine = false;

	/** @var int */
	public $linesCountBeforeDeclare = 1;

	/** @var int */
	public $linesCountAfterDeclare = 1;

	/** @var int */
	public $spacesCountAroundEqualsSign = 1;

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return [
			T_OPEN_TAG,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @param int $openTagPointer
	 */
	public function process(File $phpcsFile, $openTagPointer): void
	{
		$this->linesCountBeforeDeclare = SniffSettingsHelper::normalizeInteger($this->linesCountBeforeDeclare);
		$this->linesCountAfterDeclare = SniffSettingsHelper::normalizeInteger($this->linesCountAfterDeclare);
		$this->spacesCountAroundEqualsSign = SniffSettingsHelper::normalizeInteger($this->spacesCountAroundEqualsSign);

		if (TokenHelper::findPrevious($phpcsFile, T_OPEN_TAG, $openTagPointer - 1) !== null) {
			return;
		}

		$tokens = $phpcsFile->getTokens();
		$declarePointer = TokenHelper::findNextEffective($phpcsFile, $openTagPointer + 1);

		if ($declarePointer === null || $tokens[$declarePointer]['code'] !== T_DECLARE) {
			$fix = $phpcsFile->addFixableError(
				sprintf('Missing declare(%s).', $this->getStrictTypeDeclaration()),
				$openTagPointer,
				self::CODE_DECLARE_STRICT_TYPES_MISSING
			);
			if ($fix) {
				$phpcsFile->fixer->beginChangeset();
				$phpcsFile->fixer->addContent(
					$openTagPointer,
					sprintf('declare(%s);%s', $this->getStrictTypeDeclaration(), $phpcsFile->eolChar)
				);
				$phpcsFile->fixer->endChangeset();
			}
			return;
		}

		$strictTypesPointer = null;
		for ($i = $tokens[$declarePointer]['parenthesis_opener'] + 1; $i < $tokens[$declarePointer]['parenthesis_closer']; $i++) {
			if ($tokens[$i]['code'] !== T_STRING || $tokens[$i]['content'] !== 'strict_types') {
				continue;
			}

			$strictTypesPointer = $i;
			break;
		}

		if ($strictTypesPointer === null) {
			$fix = $phpcsFile->addFixableError(
				sprintf('Missing declare(%s).', $this->getStrictTypeDeclaration()),
				$declarePointer,
				self::CODE_DECLARE_STRICT_TYPES_MISSING
			);
			if ($fix) {
				$phpcsFile->fixer->beginChangeset();
				$phpcsFile->fixer->addContentBefore(
					$tokens[$declarePointer]['parenthesis_closer'],
					', ' . $this->getStrictTypeDeclaration()
				);
				$phpcsFile->fixer->endChangeset();
			}
			return;
		}

		/** @var int $numberPointer */
		$numberPointer = TokenHelper::findNext($phpcsFile, T_LNUMBER, $strictTypesPointer + 1);
		if ($tokens[$numberPointer]['content'] !== '1') {
			$fix = $phpcsFile->addFixableError(
				sprintf(
					'Expected %s, found %s.',
					$this->getStrictTypeDeclaration(),
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
		$format = sprintf('strict_types%1$s=%1$s1', str_repeat(' ', $this->spacesCountAroundEqualsSign));
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

				FixerHelper::change($phpcsFile, $strictTypesPointer, $numberPointer, $format);

				$phpcsFile->fixer->endChangeset();
			}
		}

		$pointerBeforeDeclare = TokenHelper::findPreviousNonWhitespace($phpcsFile, $declarePointer - 1);

		$whitespaceBefore = '';
		if ($pointerBeforeDeclare === $openTagPointer) {
			$whitespaceBefore .= substr($tokens[$openTagPointer]['content'], strlen('<?php'));
		}

		if ($pointerBeforeDeclare + 1 !== $declarePointer) {
			$whitespaceBefore .= TokenHelper::getContent($phpcsFile, $pointerBeforeDeclare + 1, $declarePointer - 1);
		}

		if ($this->declareOnFirstLine) {
			if ($whitespaceBefore !== ' ') {
				$fix = $phpcsFile->addFixableError(
					'There must be a single space between the PHP open tag and declare statement.',
					$declarePointer,
					self::CODE_INCORRECT_WHITESPACE_BEFORE_DECLARE
				);
				if ($fix) {
					$phpcsFile->fixer->beginChangeset();

					FixerHelper::change($phpcsFile, $openTagPointer, $declarePointer - 1, '<?php ');

					$phpcsFile->fixer->endChangeset();
				}
			}
		} else {
			$declareOnFirstLine = $tokens[$declarePointer]['line'] === $tokens[$openTagPointer]['line'];
			$whitespaceLinesBeforeDeclare = $this->linesCountBeforeDeclare;
			$linesCountBefore = 0;

			if (!$declareOnFirstLine) {
				$linesCountBefore = substr_count($whitespaceBefore, $phpcsFile->eolChar);

				if (
					$tokens[$pointerBeforeDeclare]['code'] === T_COMMENT
					&& CommentHelper::isLineComment($phpcsFile, $pointerBeforeDeclare)
				) {
					$whitespaceLinesBeforeDeclare--;
				} else {
					$linesCountBefore--;
				}
			}

			if ($declareOnFirstLine || $linesCountBefore !== $this->linesCountBeforeDeclare) {
				$fix = $phpcsFile->addFixableError(
					sprintf(
						'Expected %d line%s before declare statement, found %d.',
						$this->linesCountBeforeDeclare,
						$this->linesCountBeforeDeclare === 1 ? '' : 's',
						$linesCountBefore
					),
					$declarePointer,
					self::CODE_INCORRECT_WHITESPACE_BEFORE_DECLARE
				);
				if ($fix) {
					$phpcsFile->fixer->beginChangeset();

					if ($pointerBeforeDeclare === $openTagPointer) {
						$phpcsFile->fixer->replaceToken($openTagPointer, '<?php');
					}

					FixerHelper::removeBetween($phpcsFile, $pointerBeforeDeclare, $declarePointer);

					for ($i = 0; $i <= $whitespaceLinesBeforeDeclare; $i++) {
						$phpcsFile->fixer->addNewline($pointerBeforeDeclare);
					}
					$phpcsFile->fixer->endChangeset();
				}
			}
		}

		/** @var int $declareSemicolonPointer */
		$declareSemicolonPointer = TokenHelper::findNextEffective($phpcsFile, $tokens[$declarePointer]['parenthesis_closer'] + 1);
		$pointerAfterWhitespaceEnd = TokenHelper::findNextNonWhitespace($phpcsFile, $declareSemicolonPointer + 1);
		if ($pointerAfterWhitespaceEnd === null) {
			return;
		}

		$whitespaceAfter = TokenHelper::getContent($phpcsFile, $declareSemicolonPointer + 1, $pointerAfterWhitespaceEnd - 1);

		$newLinesAfter = substr_count($whitespaceAfter, $phpcsFile->eolChar);
		$linesCountAfter = $newLinesAfter > 0 ? $newLinesAfter - 1 : 0;

		if ($linesCountAfter === $this->linesCountAfterDeclare) {
			return;
		}

		$fix = $phpcsFile->addFixableError(
			sprintf(
				'Expected %d line%s after declare statement, found %d.',
				$this->linesCountAfterDeclare,
				$this->linesCountAfterDeclare === 1 ? '' : 's',
				$linesCountAfter
			),
			$declarePointer,
			self::CODE_INCORRECT_WHITESPACE_AFTER_DECLARE
		);
		if (!$fix) {
			return;
		}

		$phpcsFile->fixer->beginChangeset();

		FixerHelper::removeBetween($phpcsFile, $declareSemicolonPointer, $pointerAfterWhitespaceEnd);

		for ($i = 0; $i <= $this->linesCountAfterDeclare; $i++) {
			$phpcsFile->fixer->addNewline($declareSemicolonPointer);
		}

		$phpcsFile->fixer->endChangeset();
	}

	protected function getStrictTypeDeclaration(): string
	{
		return sprintf(
			'strict_types%s=%s1',
			str_repeat(' ', $this->spacesCountAroundEqualsSign),
			str_repeat(' ', $this->spacesCountAroundEqualsSign)
		);
	}

}
