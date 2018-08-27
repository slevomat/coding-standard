<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

use PHP_CodeSniffer\Files\File;
use function array_reverse;
use function in_array;

/**
 * @internal
 */
class ScopeHelper
{

	public static function isInSameScope(File $phpcsFile, int $firstPointer, int $secondPointer): bool
	{
		$tokens = $phpcsFile->getTokens();

		$getScope = function (int $pointer) use ($tokens): int {
			$scope = 0;

			foreach (array_reverse($tokens[$pointer]['conditions'], true) as $conditionPointer => $conditionTokenCode) {
				if (!in_array($conditionTokenCode, TokenHelper::$functionTokenCodes, true)) {
					continue;
				}

				$scope = $tokens[$conditionPointer]['level'] + 1;
				break;
			}

			return $scope;
		};

		return $getScope($firstPointer) === $getScope($secondPointer);
	}

}
