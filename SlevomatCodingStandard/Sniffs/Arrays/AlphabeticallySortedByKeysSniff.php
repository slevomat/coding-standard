<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Arrays;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\ArrayHelper;
use SlevomatCodingStandard\Helpers\ArrayKeyValue;
use SlevomatCodingStandard\Helpers\FixerHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function array_map;
use function count;
use function implode;
use function strnatcasecmp;
use function usort;

class AlphabeticallySortedByKeysSniff implements Sniff
{

	public const CODE_INCORRECT_KEY_ORDER = 'IncorrectKeyOrder';

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return TokenHelper::$arrayTokenCodes;
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @param int $stackPointer
	 */
	public function process(File $phpcsFile, $stackPointer): void
	{
		if (ArrayHelper::isMultiLine($phpcsFile, $stackPointer) === false) {
			return;
		}

		// "Parse" the array... get info for each key/value pair
		$keyValues = ArrayHelper::parse($phpcsFile, $stackPointer);

		if (ArrayHelper::isKeyedAll($keyValues) === false) {
			return;
		}

		if (ArrayHelper::isSortedByKey($keyValues)) {
			return;
		}

		$fix = $phpcsFile->addFixableError(
			'Keyed multi-line arrays must be sorted alphabetically.',
			$stackPointer,
			self::CODE_INCORRECT_KEY_ORDER,
		);
		if ($fix) {
			$this->fix($phpcsFile, $keyValues);
		}
	}

	/**
	 * @param list<ArrayKeyValue> $keyValues
	 */
	private function fix(File $phpcsFile, array $keyValues): void
	{
		$pointerStart = $keyValues[0]->getPointerStart();
		$pointerEnd = $keyValues[count($keyValues) - 1]->getPointerEnd();

		// determine indent to use
		$indent = ArrayHelper::getIndentation($keyValues);

		usort($keyValues, static fn ($a1, $a2) => strnatcasecmp((string) $a1->getKey(), (string) $a2->getKey()));

		$content = implode(
			'',
			array_map(
				static fn (ArrayKeyValue $keyValue) => $keyValue->getContent($phpcsFile, true, $indent) . $phpcsFile->eolChar,
				$keyValues,
			),
		);

		$phpcsFile->fixer->beginChangeset();
		FixerHelper::change($phpcsFile, $pointerStart, $pointerEnd, $content);
		$phpcsFile->fixer->endChangeset();
	}

}
