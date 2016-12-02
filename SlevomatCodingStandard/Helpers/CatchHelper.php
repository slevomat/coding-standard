<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

class CatchHelper
{

	/**
	 * @param \PHP_CodeSniffer_File $phpcsFile
	 * @param \SlevomatCodingStandard\Helpers\UseStatement[] $useStatements
	 * @param mixed[] $catchToken
	 * @return string[]
	 */
	public static function findCatchedTypesInCatch(\PHP_CodeSniffer_File $phpcsFile, array $useStatements, array $catchToken): array
	{
		$catchedVariablePointer = $phpcsFile->findNext(T_VARIABLE, $catchToken['parenthesis_opener'] + 1, $catchToken['parenthesis_closer']);

		$startPointer = TokenHelper::findNextEffective($phpcsFile, $catchToken['parenthesis_opener'] + 1, $catchedVariablePointer);
		$tokens = $phpcsFile->getTokens();
		$catchedTypes = [];
		while (true) {
			$nextPointer = $phpcsFile->findNext(
				array_merge([T_BITWISE_OR], TokenHelper::$nameTokenCodes),
				$startPointer,
				$catchedVariablePointer
			);
			if ($nextPointer === false) {
				break;
			}
			$nextToken = $tokens[$nextPointer];
			if ($nextToken['code'] === T_BITWISE_OR) {
				$startPointer = TokenHelper::findNextEffective($phpcsFile, $nextPointer + 1, $catchedVariablePointer);
				continue;
			}

			$endPointer = TokenHelper::findNextExcluding($phpcsFile, array_merge(
				TokenHelper::$nameTokenCodes
			), $nextPointer, $catchedVariablePointer);
			$catchedTypes[] = NamespaceHelper::resolveName(
				$phpcsFile,
				TokenHelper::getContent($phpcsFile, $startPointer, $endPointer),
				$useStatements,
				$catchToken['parenthesis_opener']
			);

			$startPointer = $endPointer + 1;
		}

		return $catchedTypes;
	}

}
