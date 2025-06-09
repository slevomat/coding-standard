<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\ClassHelper;
use SlevomatCodingStandard\Helpers\PropertyHelper;
use SlevomatCodingStandard\Helpers\StringHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function array_map;
use function in_array;
use const T_AS;
use const T_CLASS;
use const T_CONST;
use const T_FUNCTION;
use const T_PRIVATE;
use const T_PRIVATE_SET;
use const T_PROTECTED;
use const T_PROTECTED_SET;
use const T_READONLY;
use const T_VARIABLE;

final class ForbiddenPublicPropertySniff implements Sniff
{

	public const CODE_FORBIDDEN_PUBLIC_PROPERTY = 'ForbiddenPublicProperty';

	public bool $allowReadonly = false;

	public bool $allowNonPublicSet = true;

	public bool $checkPromoted = false;

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return TokenHelper::PROPERTY_MODIFIERS_TOKEN_CODES;
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @param int $pointer
	 */
	public function process(File $phpcsFile, $pointer): void
	{
		$tokens = $phpcsFile->getTokens();

		$asPointer = TokenHelper::findPreviousEffective($phpcsFile, $pointer - 1);
		if ($tokens[$asPointer]['code'] === T_AS) {
			return;
		}

		$nextPointer = TokenHelper::findNextEffective($phpcsFile, $pointer + 1);
		if (in_array($tokens[$nextPointer]['code'], TokenHelper::PROPERTY_MODIFIERS_TOKEN_CODES, true)) {
			// We don't want to report the same property multiple times
			return;
		}

		// Ignore other class members with same mofidiers
		$propertyPointer = TokenHelper::findNext($phpcsFile, [T_VARIABLE, T_CONST, T_FUNCTION, T_CLASS], $pointer + 1);
		if (
			$propertyPointer === null
			|| $tokens[$propertyPointer]['code'] !== T_VARIABLE
			|| !PropertyHelper::isProperty($phpcsFile, $propertyPointer, $this->checkPromoted)
		) {
			return;
		}

		// Skip sniff classes, they have public properties for configuration (unfortunately)
		if ($this->isSniffClass($phpcsFile, $propertyPointer)) {
			return;
		}

		$propertyStartPointer = PropertyHelper::getStartPointer($phpcsFile, $propertyPointer);

		$modifiersPointers = TokenHelper::findNextAll(
			$phpcsFile,
			TokenHelper::PROPERTY_MODIFIERS_TOKEN_CODES,
			$propertyStartPointer,
			$propertyPointer,
		);
		$modifiersCodes = array_map(static fn (int $modifierPointer) => $tokens[$modifierPointer]['code'], $modifiersPointers);

		if (in_array(T_PROTECTED, $modifiersCodes, true) || in_array(T_PRIVATE, $modifiersCodes, true)) {
			return;
		}

		if ($this->allowReadonly && in_array(T_READONLY, $modifiersCodes, true)) {
			return;
		}

		if (
			$this->allowNonPublicSet
			&& (
				in_array(T_PROTECTED_SET, $modifiersCodes, true)
				|| in_array(T_PRIVATE_SET, $modifiersCodes, true)
			)
		) {
			return;
		}

		$phpcsFile->addError(
			'Do not use public properties. Use method access instead.',
			$propertyPointer,
			self::CODE_FORBIDDEN_PUBLIC_PROPERTY,
		);
	}

	private function isSniffClass(File $phpcsFile, int $position): bool
	{
		$classTokenPosition = ClassHelper::getClassPointer($phpcsFile, $position);
		$classNameToken = ClassHelper::getName($phpcsFile, $classTokenPosition);

		return StringHelper::endsWith($classNameToken, 'Sniff');
	}

}
