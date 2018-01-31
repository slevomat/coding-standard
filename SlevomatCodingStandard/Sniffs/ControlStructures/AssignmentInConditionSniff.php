<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\ControlStructures;

use SlevomatCodingStandard\Helpers\TokenHelper;

class AssignmentInConditionSniff implements \PHP_CodeSniffer\Sniffs\Sniff
{

	public const CODE_ASSIGNMENT_IN_CONDITION = 'AssignmentInCondition';

	/**
	 * @return mixed[]
	 */
	public function register(): array
	{
		return [
			T_IF,
			T_ELSEIF,
			T_DO,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $conditionStartPointer
	 */
	public function process(\PHP_CodeSniffer\Files\File $phpcsFile, $conditionStartPointer): void
	{
		$tokens = $phpcsFile->getTokens();
		$token = $tokens[$conditionStartPointer];
		if ($token['code'] === T_DO) {
			$whilePointer = TokenHelper::findNext($phpcsFile, T_WHILE, $token['scope_closer'] + 1);
			$whileToken = $tokens[$whilePointer];
			$parenthesisOpener = $whileToken['parenthesis_opener'];
			$parenthesisCloser = $whileToken['parenthesis_closer'];
			$type = 'do-while';
		} else {
			$parenthesisOpener = $token['parenthesis_opener'];
			$parenthesisCloser = $token['parenthesis_closer'];
			$type = $token['code'] === T_IF ? 'if' : 'elseif';
		}
		$this->processCondition($phpcsFile, $parenthesisOpener, $parenthesisCloser, $type);
	}

	private function processCondition(
		\PHP_CodeSniffer\Files\File $phpcsFile,
		int $parenthesisOpener,
		int $parenthesisCloser,
		string $conditionType
	): void
	{
		$equalsTokenPointer = TokenHelper::findNext($phpcsFile, T_EQUAL, $parenthesisOpener + 1, $parenthesisCloser);
		if ($equalsTokenPointer === null) {
			return;
		}

		$phpcsFile->addError(
			sprintf('Assignment in %s condition is not allowed.', $conditionType),
			$equalsTokenPointer,
			self::CODE_ASSIGNMENT_IN_CONDITION
		);
	}

}
