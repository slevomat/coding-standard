<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\FunctionHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function sprintf;
use const T_ANON_CLASS;
use const T_CLASS;
use const T_FUNCTION;
use const T_INTERFACE;
use const T_TRAIT;

class MethodPerClassLimitSniff implements Sniff
{

	public const CODE_METHOD_PER_CLASS_LIMIT = 'MethodPerClassLimit';

	/** @var int */
	public $maxMethodCount = 10;

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return [T_CLASS, T_ANON_CLASS, T_TRAIT, T_INTERFACE];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @param File $phpcsFile
	 * @param int $classPointer
	 */
	public function process(File $phpcsFile, $classPointer): void
	{
		$tokens = $phpcsFile->getTokens();
		$token = $tokens[$classPointer];
		$scopeOpenerPointer = $token['scope_opener'];
		$scopeCloserPointer = $token['scope_closer'];
		$numberOfMethods = 0;
		$acceptedLevel = $tokens[$scopeOpenerPointer]['level'] + 1;
		foreach (TokenHelper::findNextAll($phpcsFile, T_FUNCTION, $scopeOpenerPointer + 1, $scopeCloserPointer) as $functionPointer) {
			if (FunctionHelper::isMethod($phpcsFile, $functionPointer) && ($tokens[$functionPointer]['level'] === $acceptedLevel)) {
				$numberOfMethods++;
			}
		}
		if ($numberOfMethods <= $this->maxMethodCount) {
			return;
		}
		$errorMessage = sprintf(
			'%s has too many methods: %d. Can be up to %d methods.',
			$phpcsFile->getTokens()[$classPointer]['content'],
			$numberOfMethods,
			$this->maxMethodCount
		);
		$phpcsFile->addError($errorMessage, $classPointer, self::CODE_METHOD_PER_CLASS_LIMIT);
	}

}
