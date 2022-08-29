<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\PropertyHelper;
use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use SlevomatCodingStandard\Helpers\TypeHintHelper;
use UnexpectedValueException;
use function array_key_exists;
use function array_keys;
use function array_map;
use function asort;
use function count;
use function implode;
use function in_array;
use function preg_split;
use function sprintf;
use function strtolower;
use const T_AS;
use const T_CONST;
use const T_DOUBLE_COLON;
use const T_FUNCTION;
use const T_NULLABLE;
use const T_PRIVATE;
use const T_PROTECTED;
use const T_PUBLIC;
use const T_READONLY;
use const T_STATIC;
use const T_VAR;
use const T_VARIABLE;
use const T_WHITESPACE;

class PropertyDeclarationSniff implements Sniff
{

	public const CODE_NO_SPACE_BEFORE_NULLABILITY_SYMBOL = 'NoSpaceBeforeNullabilitySymbol';

	public const CODE_MULTIPLE_SPACES_BEFORE_NULLABILITY_SYMBOL = 'MultipleSpacesBeforeNullabilitySymbol';

	public const CODE_MULTIPLE_SPACES_BEFORE_TYPE_HINT = 'MultipleSpacesBeforeTypeHint';

	public const CODE_NO_SPACE_BETWEEN_TYPE_HINT_AND_PROPERTY = 'NoSpaceBetweenTypeHintAndProperty';

	public const CODE_MULTIPLE_SPACES_BETWEEN_TYPE_HINT_AND_PROPERTY = 'MultipleSpacesBetweenTypeHintAndProperty';

	public const CODE_WHITESPACE_AFTER_NULLABILITY_SYMBOL = 'WhitespaceAfterNullabilitySymbol';

	public const CODE_INCORRECT_ORDER_OF_MODIFIERS = 'IncorrectOrderOfModifiers';

	public const CODE_MULTIPLE_SPACES_BETWEEN_MODIFIERS = 'MultipleSpacesBetweenModifiers';

	/** @var string[]|null */
	public $modifiersOrder = [];

	/** @var bool */
	public $checkPromoted = false;

	/** @var bool */
	public $enableMultipleSpacesBetweenModifiersCheck = false;

	/** @var array<int, array<int, (int|string)>>|null */
	private $normalizedModifiersOrder = null;

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return TokenHelper::$propertyModifiersTokenCodes;
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @param int $modifierPointer
	 */
	public function process(File $phpcsFile, $modifierPointer): void
	{
		$tokens = $phpcsFile->getTokens();

		$asPointer = TokenHelper::findPreviousEffective($phpcsFile, $modifierPointer - 1);
		if ($tokens[$asPointer]['code'] === T_AS) {
			return;
		}

		$nextPointer = TokenHelper::findNextEffective($phpcsFile, $modifierPointer + 1);
		if (in_array($tokens[$nextPointer]['code'], TokenHelper::$propertyModifiersTokenCodes, true)) {
			// We don't want to report the some property twice
			return;
		}

		if ($tokens[$nextPointer]['code'] === T_DOUBLE_COLON) {
			// Ignore static::
			return;
		}

		$propertyPointer = TokenHelper::findNext($phpcsFile, [T_FUNCTION, T_CONST, T_VARIABLE], $modifierPointer + 1);

		if ($propertyPointer === null || $tokens[$propertyPointer]['code'] !== T_VARIABLE) {
			return;
		}

		if (!PropertyHelper::isProperty($phpcsFile, $propertyPointer, $this->checkPromoted)) {
			return;
		}

		$firstModifierPointer = $modifierPointer;
		do {
			$previousPointer = TokenHelper::findPreviousEffective($phpcsFile, $firstModifierPointer - 1);
			if (!in_array($tokens[$previousPointer]['code'], TokenHelper::$propertyModifiersTokenCodes, true)) {
				break;
			}

			$firstModifierPointer = $previousPointer;
		} while (true);

		$this->checkModifiersOrder($phpcsFile, $propertyPointer, $firstModifierPointer, $modifierPointer);
		$this->checkSpacesBetweenModifiers($phpcsFile, $propertyPointer, $firstModifierPointer, $modifierPointer);
		$this->checkTypeHintSpacing($phpcsFile, $propertyPointer, $modifierPointer);
	}

	private function checkModifiersOrder(File $phpcsFile, int $propertyPointer, int $firstModifierPointer, int $lastModifierPointer): void
	{
		$modifiersPointers = TokenHelper::findNextAll(
			$phpcsFile,
			TokenHelper::$propertyModifiersTokenCodes,
			$firstModifierPointer,
			$lastModifierPointer + 1
		);

		if (count($modifiersPointers) < 2) {
			return;
		}

		$tokens = $phpcsFile->getTokens();
		$modifiersGroups = $this->getNormalizedModifiersOrder();

		$expectedModifiersPositions = [];
		foreach ($modifiersPointers as $modifierPointer) {
			for ($i = 0; $i < count($modifiersGroups); $i++) {
				if (in_array($tokens[$modifierPointer]['code'], $modifiersGroups[$i], true)) {
					$expectedModifiersPositions[$modifierPointer] = $i;
					continue 2;
				}
			}

			// Modifier position is not defined so add it to the end
			$expectedModifiersPositions[$modifierPointer] = count($modifiersGroups);
		}

		$error = false;

		for ($i = 1; $i < count($modifiersPointers); $i++) {
			for ($j = 0; $j < $i; $j++) {
				if ($expectedModifiersPositions[$modifiersPointers[$i]] < $expectedModifiersPositions[$modifiersPointers[$j]]) {
					$error = true;
					break;
				}
			}
		}

		if (!$error) {
			return;
		}

		$actualModifiers = array_map(static function (int $modifierPointer) use ($tokens): string {
			return $tokens[$modifierPointer]['content'];
		}, $modifiersPointers);
		$actualModifiersFormatted = implode(' ', $actualModifiers);

		asort($expectedModifiersPositions);
		$expectedModifiers = array_map(static function (int $modifierPointer) use ($tokens): string {
			return $tokens[$modifierPointer]['content'];
		}, array_keys($expectedModifiersPositions));
		$expectedModifiersFormatted = implode(' ', $expectedModifiers);

		$fix = $phpcsFile->addFixableError(
			sprintf(
				'Incorrect order of modifiers "%s" of property %s, expected "%s".',
				$actualModifiersFormatted,
				$tokens[$propertyPointer]['content'],
				$expectedModifiersFormatted
			),
			$firstModifierPointer,
			self::CODE_INCORRECT_ORDER_OF_MODIFIERS
		);
		if (!$fix) {
			return;
		}

		$phpcsFile->fixer->beginChangeset();

		$phpcsFile->fixer->replaceToken($firstModifierPointer, $expectedModifiersFormatted);

		for ($i = $firstModifierPointer + 1; $i <= $lastModifierPointer; $i++) {
			$phpcsFile->fixer->replaceToken($i, '');
		}

		$phpcsFile->fixer->endChangeset();
	}

	private function checkSpacesBetweenModifiers(
		File $phpcsFile,
		int $propertyPointer,
		int $firstModifierPointer,
		int $lastModifierPointer
	): void
	{
		if (!$this->enableMultipleSpacesBetweenModifiersCheck) {
			return;
		}

		$modifiersPointers = TokenHelper::findNextAll(
			$phpcsFile,
			TokenHelper::$propertyModifiersTokenCodes,
			$firstModifierPointer,
			$lastModifierPointer + 1
		);

		if (count($modifiersPointers) < 2) {
			return;
		}

		$tokens = $phpcsFile->getTokens();

		$error = false;
		for ($i = 0; $i < count($modifiersPointers) - 1; $i++) {
			$whitespace = TokenHelper::getContent($phpcsFile, $modifiersPointers[$i] + 1, $modifiersPointers[$i + 1] - 1);
			if ($whitespace !== ' ') {
				$error = true;
				break;
			}
		}

		if (!$error) {
			return;
		}

		$fix = $phpcsFile->addFixableError(
			sprintf('There must be exactly one space between modifiers of property %s.', $tokens[$propertyPointer]['content']),
			$firstModifierPointer,
			self::CODE_MULTIPLE_SPACES_BETWEEN_MODIFIERS
		);
		if (!$fix) {
			return;
		}

		$expectedModifiers = array_map(static function (int $modifierPointer) use ($tokens): string {
			return $tokens[$modifierPointer]['content'];
		}, $modifiersPointers);
		$expectedModifiersFormatted = implode(' ', $expectedModifiers);

		$phpcsFile->fixer->beginChangeset();

		$phpcsFile->fixer->replaceToken($firstModifierPointer, $expectedModifiersFormatted);

		for ($i = $firstModifierPointer + 1; $i <= $lastModifierPointer; $i++) {
			$phpcsFile->fixer->replaceToken($i, '');
		}

		$phpcsFile->fixer->endChangeset();
	}

	private function checkTypeHintSpacing(File $phpcsFile, int $propertyPointer, int $lastModifierPointer): void
	{
		$typeHintEndPointer = TokenHelper::findPrevious(
			$phpcsFile,
			TokenHelper::getTypeHintTokenCodes(),
			$propertyPointer - 1,
			$lastModifierPointer
		);
		if ($typeHintEndPointer === null) {
			return;
		}

		$tokens = $phpcsFile->getTokens();

		$typeHintStartPointer = TypeHintHelper::getStartPointer($phpcsFile, $typeHintEndPointer);

		$previousPointer = TokenHelper::findPreviousEffective($phpcsFile, $typeHintStartPointer - 1, $lastModifierPointer);
		$nullabilitySymbolPointer = $previousPointer !== null && $tokens[$previousPointer]['code'] === T_NULLABLE ? $previousPointer : null;

		$propertyName = $tokens[$propertyPointer]['content'];

		if ($tokens[$lastModifierPointer + 1]['code'] !== T_WHITESPACE) {
			$errorMessage = sprintf('There must be exactly one space before type hint nullability symbol of property %s.', $propertyName);
			$errorCode = self::CODE_NO_SPACE_BEFORE_NULLABILITY_SYMBOL;

			$fix = $phpcsFile->addFixableError($errorMessage, $typeHintEndPointer, $errorCode);
			if ($fix) {
				$phpcsFile->fixer->beginChangeset();
				$phpcsFile->fixer->addContent($lastModifierPointer, ' ');
				$phpcsFile->fixer->endChangeset();
			}
		} elseif ($tokens[$lastModifierPointer + 1]['content'] !== ' ') {
			if ($nullabilitySymbolPointer !== null) {
				$errorMessage = sprintf(
					'There must be exactly one space before type hint nullability symbol of property %s.',
					$propertyName
				);
				$errorCode = self::CODE_MULTIPLE_SPACES_BEFORE_NULLABILITY_SYMBOL;
			} else {
				$errorMessage = sprintf('There must be exactly one space before type hint of property %s.', $propertyName);
				$errorCode = self::CODE_MULTIPLE_SPACES_BEFORE_TYPE_HINT;
			}

			$fix = $phpcsFile->addFixableError($errorMessage, $lastModifierPointer, $errorCode);
			if ($fix) {
				$phpcsFile->fixer->beginChangeset();
				$phpcsFile->fixer->replaceToken($lastModifierPointer + 1, ' ');
				$phpcsFile->fixer->endChangeset();
			}
		}

		if ($tokens[$typeHintEndPointer + 1]['code'] !== T_WHITESPACE) {
			$fix = $phpcsFile->addFixableError(
				sprintf('There must be exactly one space between type hint and property %s.', $propertyName),
				$typeHintEndPointer,
				self::CODE_NO_SPACE_BETWEEN_TYPE_HINT_AND_PROPERTY
			);
			if ($fix) {
				$phpcsFile->fixer->beginChangeset();
				$phpcsFile->fixer->addContent($typeHintEndPointer, ' ');
				$phpcsFile->fixer->endChangeset();
			}
		} elseif ($tokens[$typeHintEndPointer + 1]['content'] !== ' ') {
			$fix = $phpcsFile->addFixableError(
				sprintf('There must be exactly one space between type hint and property %s.', $propertyName),
				$typeHintEndPointer,
				self::CODE_MULTIPLE_SPACES_BETWEEN_TYPE_HINT_AND_PROPERTY
			);
			if ($fix) {
				$phpcsFile->fixer->beginChangeset();
				$phpcsFile->fixer->replaceToken($typeHintEndPointer + 1, ' ');
				$phpcsFile->fixer->endChangeset();
			}
		}

		if ($nullabilitySymbolPointer === null) {
			return;
		}

		if ($nullabilitySymbolPointer + 1 === $typeHintStartPointer) {
			return;
		}

		$fix = $phpcsFile->addFixableError(
			sprintf('There must be no whitespace between type hint nullability symbol and type hint of property %s.', $propertyName),
			$typeHintStartPointer,
			self::CODE_WHITESPACE_AFTER_NULLABILITY_SYMBOL
		);
		if (!$fix) {
			return;
		}

		$phpcsFile->fixer->beginChangeset();
		$phpcsFile->fixer->replaceToken($nullabilitySymbolPointer + 1, '');
		$phpcsFile->fixer->endChangeset();
	}

	/**
	 * @return array<int, array<int, (int|string)>>
	 */
	private function getNormalizedModifiersOrder(): array
	{
		if ($this->normalizedModifiersOrder === null) {
			$modifiersGroups = SniffSettingsHelper::normalizeArray($this->modifiersOrder);

			if ($modifiersGroups === []) {
				$modifiersGroups = [
					'var, public, protected, private',
					'static, readonly',
				];
			}

			$this->normalizedModifiersOrder = [];

			$mapping = [
				'var' => T_VAR,
				'public' => T_PUBLIC,
				'protected' => T_PROTECTED,
				'private' => T_PRIVATE,
				'static' => T_STATIC,
				'readonly' => T_READONLY,
			];

			foreach ($modifiersGroups as $modifiersGroupNo => $modifiersGroup) {
				$this->normalizedModifiersOrder[$modifiersGroupNo] = [];

				/** @var string[] $modifiers */
				$modifiers = preg_split('~\\s*,\\s*~', strtolower($modifiersGroup));

				foreach ($modifiers as $modifier) {
					if (!array_key_exists($modifier, $mapping)) {
						throw new UnexpectedValueException(sprintf('Unknown property modifier "%s".', $modifier));
					}

					$this->normalizedModifiersOrder[$modifiersGroupNo][] = $mapping[$modifier];
				}
			}
		}

		return $this->normalizedModifiersOrder;
	}

}
