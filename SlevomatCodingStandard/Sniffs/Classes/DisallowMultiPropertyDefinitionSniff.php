<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\DocCommentHelper;
use SlevomatCodingStandard\Helpers\FixerHelper;
use SlevomatCodingStandard\Helpers\IndentationHelper;
use SlevomatCodingStandard\Helpers\PropertyHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function count;
use function in_array;
use function sprintf;
use function trim;
use const T_ARRAY;
use const T_AS;
use const T_CLASS;
use const T_COMMA;
use const T_CONST;
use const T_FUNCTION;
use const T_OPEN_CURLY_BRACKET;
use const T_OPEN_SHORT_ARRAY;
use const T_SEMICOLON;
use const T_VARIABLE;

class DisallowMultiPropertyDefinitionSniff implements Sniff
{

	public const CODE_DISALLOWED_MULTI_PROPERTY_DEFINITION = 'DisallowedMultiPropertyDefinition';

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return TokenHelper::PROPERTY_MODIFIERS_TOKEN_CODES;
	}

	public function process(File $phpcsFile, int $modifierPointer): void
	{
		$tokens = $phpcsFile->getTokens();

		$asPointer = TokenHelper::findPreviousEffective($phpcsFile, $modifierPointer - 1);
		if ($tokens[$asPointer]['code'] === T_AS) {
			return;
		}

		$nextPointer = TokenHelper::findNextEffective($phpcsFile, $modifierPointer + 1);
		if (in_array($tokens[$nextPointer]['code'], TokenHelper::PROPERTY_MODIFIERS_TOKEN_CODES, true)) {
			// We don't want to report the same property multiple times
			return;
		}

		// Ignore other class members with same modifiers
		$propertyPointer = TokenHelper::findNext($phpcsFile, [T_VARIABLE, T_CONST, T_FUNCTION, T_CLASS], $modifierPointer + 1);
		if (
			$propertyPointer === null
			|| $tokens[$propertyPointer]['code'] !== T_VARIABLE
			|| !PropertyHelper::isProperty($phpcsFile, $propertyPointer)
		) {
			return;
		}

		$endPointer = TokenHelper::findNext($phpcsFile, [T_SEMICOLON, T_OPEN_CURLY_BRACKET], $propertyPointer + 1);
		if ($tokens[$endPointer]['code'] === T_OPEN_CURLY_BRACKET) {
			return;
		}

		$semicolonPointer = TokenHelper::findNext($phpcsFile, T_SEMICOLON, $propertyPointer + 1);
		$commaPointers = [];
		$nextPointer = $propertyPointer;
		do {
			$nextPointer = TokenHelper::findNext($phpcsFile, [T_COMMA, T_OPEN_SHORT_ARRAY, T_ARRAY], $nextPointer + 1, $semicolonPointer);

			if ($nextPointer === null) {
				break;
			}

			if ($tokens[$nextPointer]['code'] === T_OPEN_SHORT_ARRAY) {
				$nextPointer = $tokens[$nextPointer]['bracket_closer'];
				continue;
			}

			if ($tokens[$nextPointer]['code'] === T_ARRAY) {
				$nextPointer = $tokens[$nextPointer]['parenthesis_closer'];
				continue;
			}

			$commaPointers[] = $nextPointer;

		} while (true);

		if (count($commaPointers) === 0) {
			return;
		}

		$fix = $phpcsFile->addFixableError(
			'Use of multi property definition is disallowed.',
			$modifierPointer,
			self::CODE_DISALLOWED_MULTI_PROPERTY_DEFINITION,
		);
		if (!$fix) {
			return;
		}

		$propertyStartPointer = PropertyHelper::getStartPointer($phpcsFile, $propertyPointer);
		$pointerBeforeProperty = TokenHelper::findPreviousEffective($phpcsFile, $propertyPointer - 1);
		$pointerBeforeSemicolon = TokenHelper::findPreviousEffective($phpcsFile, $semicolonPointer - 1);

		$indentation = IndentationHelper::getIndentation($phpcsFile, $propertyStartPointer);

		$docCommentPointer = DocCommentHelper::findDocCommentOpenPointer($phpcsFile, $propertyPointer);
		$docComment = $docCommentPointer !== null
			? trim(TokenHelper::getContent($phpcsFile, $docCommentPointer, $tokens[$docCommentPointer]['comment_closer']))
			: null;

		$data = [];
		foreach ($commaPointers as $commaPointer) {
			$data[$commaPointer] = [
				'pointerBeforeComma' => TokenHelper::findPreviousEffective($phpcsFile, $commaPointer - 1),
				'pointerAfterComma' => TokenHelper::findNextEffective($phpcsFile, $commaPointer + 1),
			];
		}

		$propertyContent = TokenHelper::getContent($phpcsFile, $propertyStartPointer, $pointerBeforeProperty);

		$phpcsFile->fixer->beginChangeset();

		FixerHelper::change($phpcsFile, $pointerBeforeProperty + 1, $propertyPointer - 1, ' ');

		foreach ($commaPointers as $commaPointer) {
			FixerHelper::removeBetween($phpcsFile, $data[$commaPointer]['pointerBeforeComma'], $commaPointer);
			FixerHelper::replace(
				$phpcsFile,
				$commaPointer,
				sprintf(
					';%s%s%s%s ',
					$phpcsFile->eolChar,
					$docComment !== null
						? sprintf('%s%s%s', $indentation, $docComment, $phpcsFile->eolChar)
						: '',
					$indentation,
					$propertyContent,
				),
			);

			FixerHelper::removeBetween($phpcsFile, $commaPointer, $data[$commaPointer]['pointerAfterComma']);
		}

		FixerHelper::removeBetween($phpcsFile, $pointerBeforeSemicolon, $semicolonPointer);

		$phpcsFile->fixer->endChangeset();
	}

}
