<?php

namespace SlevomatCodingStandard\Sniffs\ControlStructures;

class AssignmentInConditionSniff implements \PHP_CodeSniffer_Sniff
{

	const CODE_ASSIGNMENT_IN_CONDITION = 'assignmentInCondition';

	/**
	 * @return integer[]
	 */
	public function register()
	{
		return [
			T_IF,
			T_ELSEIF,
			T_DO,
		];
	}

	/**
	 * @param \PHP_CodeSniffer_File $phpcsFile
	 * @param integer $conditionStartPointer
	 */
	public function process(\PHP_CodeSniffer_File $phpcsFile, $conditionStartPointer)
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

	/**
	 * @param \PHP_CodeSniffer_File $phpcsFile
	 * @param integer $parenthesisOpener
	 * @param integer $parenthesisCloser
	 * @param string $conditionType
	 */
	private function processCondition(
		\PHP_CodeSniffer_File $phpcsFile,
		$parenthesisOpener,
		$parenthesisCloser,
		$conditionType
	)
	{
		$equalsTokenPointer = $phpcsFile->findNext(T_EQUAL, $parenthesisOpener + 1, $parenthesisCloser);
		if ($equalsTokenPointer !== false) {
			$phpcsFile->addError(
				sprintf('Assignment in %s condition is not allowed', $conditionType),
				$equalsTokenPointer,
				self::CODE_ASSIGNMENT_IN_CONDITION
			);
		}
	}

}
