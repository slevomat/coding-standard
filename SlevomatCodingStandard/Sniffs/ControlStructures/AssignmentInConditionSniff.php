<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\ControlStructures;

class AssignmentInConditionSniff implements \PHP_CodeSniffer\Sniffs\Sniff
{

	const CODE_ASSIGNMENT_IN_CONDITION = 'AssignmentInCondition';

	/**
	 * @return int[]
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
	public function process(\PHP_CodeSniffer\Files\File $phpcsFile, $conditionStartPointer)
	{
		$tokens = $phpcsFile->getTokens();
		$token = $tokens[$conditionStartPointer];
		if ($token['code'] === T_DO) {
			$whilePointer = $phpcsFile->findNext(T_WHILE, $token['scope_closer'] + 1);
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
	)
	{
		$equalsTokenPointer = $phpcsFile->findNext(T_EQUAL, $parenthesisOpener + 1, $parenthesisCloser);
		if ($equalsTokenPointer !== false) {
			$phpcsFile->addError(
				sprintf('Assignment in %s condition is not allowed.', $conditionType),
				$equalsTokenPointer,
				self::CODE_ASSIGNMENT_IN_CONDITION
			);
		}
	}

}
