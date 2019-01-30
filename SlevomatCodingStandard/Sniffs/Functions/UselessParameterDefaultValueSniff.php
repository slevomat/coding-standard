<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Functions;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function array_key_exists;
use function count;
use function sprintf;
use function strtolower;
use const T_CLOSURE;
use const T_COMMA;
use const T_FUNCTION;

class UselessParameterDefaultValueSniff implements Sniff
{

	public const CODE_USELESS_PARAMETER_DEFAULT_VALUE = 'UselessParameterDefaultValue';

	/**
	 * @return (int|string)[]
	 */
	public function register(): array
	{
		return [
			T_FUNCTION,
			T_CLOSURE,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $functionPointer
	 */
	public function process(File $phpcsFile, $functionPointer): void
	{
		$parameters = $phpcsFile->getMethodParameters($functionPointer);
		$parametersCount = count($parameters);

		if ($parametersCount === 0) {
			return;
		}

		for ($i = 0; $i < $parametersCount; $i++) {
			$parameter = $parameters[$i];

			if (!array_key_exists('default', $parameter)) {
				continue;
			}

			$defaultValue = strtolower($parameter['default']);
			if ($defaultValue === 'null' && !$parameter['nullable_type']) {
				continue;
			}

			for ($j = $i + 1; $j < $parametersCount; $j++) {
				$nextParameter = $parameters[$j];

				if (array_key_exists('default', $nextParameter)) {
					continue;
				}

				$fix = $phpcsFile->addFixableError(
					sprintf('Useless default value of parameter %s.', $parameter['name']),
					$parameter['token'],
					self::CODE_USELESS_PARAMETER_DEFAULT_VALUE
				);

				if (!$fix) {
					continue;
				}

				$commaPointer = TokenHelper::findPrevious($phpcsFile, T_COMMA, $parameters[$i + 1]['token'] - 1);

				$phpcsFile->fixer->beginChangeset();
				for ($k = $parameter['token'] + 1; $k < $commaPointer; $k++) {
					$phpcsFile->fixer->replaceToken($k, '');
				}
				$phpcsFile->fixer->endChangeset();

				break;
			}
		}
	}

}
