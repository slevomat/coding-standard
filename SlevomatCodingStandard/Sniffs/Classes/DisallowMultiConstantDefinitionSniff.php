<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;
use SlevomatCodingStandard\Helpers\DocCommentHelper;
use SlevomatCodingStandard\Helpers\FixerHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function count;
use function in_array;
use function sprintf;
use function trim;
use const T_COMMA;
use const T_CONST;
use const T_EQUAL;
use const T_FINAL;
use const T_OPEN_SHORT_ARRAY;
use const T_SEMICOLON;

class DisallowMultiConstantDefinitionSniff implements Sniff
{

	public const CODE_DISALLOWED_MULTI_CONSTANT_DEFINITION = 'DisallowedMultiConstantDefinition';

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return [T_CONST];
	}

	public function process(File $phpcsFile, int $constantPointer): void
	{
		$tokens = $phpcsFile->getTokens();

		$semicolonPointer = TokenHelper::findNext($phpcsFile, T_SEMICOLON, $constantPointer + 1);
		$commaPointers = [];
		$nextPointer = $constantPointer;
		do {
			$nextPointer = TokenHelper::findNext($phpcsFile, [T_COMMA, T_OPEN_SHORT_ARRAY], $nextPointer + 1, $semicolonPointer);

			if ($nextPointer === null) {
				break;
			}

			if ($tokens[$nextPointer]['code'] === T_OPEN_SHORT_ARRAY) {
				$nextPointer = $tokens[$nextPointer]['bracket_closer'];
				continue;
			}

			$commaPointers[] = $nextPointer;

		} while (true);

		if (count($commaPointers) === 0) {
			return;
		}

		$fix = $phpcsFile->addFixableError(
			'Use of multi constant definition is disallowed.',
			$constantPointer,
			self::CODE_DISALLOWED_MULTI_CONSTANT_DEFINITION,
		);
		if (!$fix) {
			return;
		}

		$firstModifierPointer = $constantPointer;
		$modifiers = '';
		$possibleModifierPointer = TokenHelper::findPreviousEffective($phpcsFile, $constantPointer - 1);
		while (
			$tokens[$possibleModifierPointer]['code'] === T_FINAL
			|| in_array($tokens[$possibleModifierPointer]['code'], Tokens::SCOPE_MODIFIERS, true)
		) {
			$firstModifierPointer = $possibleModifierPointer;
			$modifiers = sprintf('%s %s', $tokens[$possibleModifierPointer]['content'], $modifiers);
			$possibleModifierPointer = TokenHelper::findPreviousEffective($phpcsFile, $possibleModifierPointer - 1);
		}

		$pointerAfterConst = TokenHelper::findNextEffective($phpcsFile, $constantPointer + 1);
		$pointerBeforeSemicolon = TokenHelper::findPreviousEffective($phpcsFile, $semicolonPointer - 1);

		$equalPointer = TokenHelper::findNext($phpcsFile, T_EQUAL, $pointerAfterConst + 1, $semicolonPointer);
		$namePointer = TokenHelper::findPreviousEffective($phpcsFile, $equalPointer - 1);
		$typeHint = $namePointer !== $pointerAfterConst
			? sprintf(
				'%s ',
				TokenHelper::getContent(
					$phpcsFile,
					$pointerAfterConst,
					TokenHelper::findPreviousEffective($phpcsFile, $namePointer - 1),
				),
			)
			: '';

		// Original content is used, because tabs are replaced with spaces in token content when tab width is set
		$firstPointerOnLine = TokenHelper::findFirstTokenOnLine($phpcsFile, $firstModifierPointer);
		$indentation = $phpcsFile->getTokensAsString($firstPointerOnLine, $firstModifierPointer - $firstPointerOnLine, true);

		$docCommentPointer = DocCommentHelper::findDocCommentOpenPointer($phpcsFile, $constantPointer);
		$docComment = $docCommentPointer !== null
			? trim($phpcsFile->getTokensAsString(
				$docCommentPointer,
				$tokens[$docCommentPointer]['comment_closer'] - $docCommentPointer + 1,
				true,
			))
			: null;

		$data = [];
		foreach ($commaPointers as $commaPointer) {
			$data[$commaPointer] = [
				'pointerBeforeComma' => TokenHelper::findPreviousEffective($phpcsFile, $commaPointer - 1),
				'pointerAfterComma' => TokenHelper::findNextEffective($phpcsFile, $commaPointer + 1),
			];
		}

		$phpcsFile->fixer->beginChangeset();

		FixerHelper::add($phpcsFile, $constantPointer, ' ');

		FixerHelper::removeBetween($phpcsFile, $constantPointer, $pointerAfterConst);

		foreach ($commaPointers as $commaPointer) {
			FixerHelper::removeBetween($phpcsFile, $data[$commaPointer]['pointerBeforeComma'], $commaPointer);
			// Indentation is copied as written, FixerHelper::replace() would convert spaces to tabs
			$phpcsFile->fixer->replaceToken(
				$commaPointer,
				sprintf(
					';%s%s%s%sconst %s',
					$phpcsFile->eolChar,
					$docComment !== null
						? sprintf('%s%s%s', $indentation, $docComment, $phpcsFile->eolChar)
						: '',
					$indentation,
					$modifiers,
					$typeHint,
				),
			);

			FixerHelper::removeBetween($phpcsFile, $commaPointer, $data[$commaPointer]['pointerAfterComma']);
		}

		FixerHelper::removeBetween($phpcsFile, $pointerBeforeSemicolon, $semicolonPointer);

		$phpcsFile->fixer->endChangeset();
	}

}
