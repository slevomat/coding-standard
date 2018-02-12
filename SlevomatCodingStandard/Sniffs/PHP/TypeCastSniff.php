<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\PHP;

use SlevomatCodingStandard\Helpers\TokenHelper;

class TypeCastSniff implements \PHP_CodeSniffer\Sniffs\Sniff
{

	public const CODE_FORBIDDEN_CAST_USED = 'ForbiddenCastUsed';
	public const CODE_INVALID_CAST_USED = 'InvalidCastUsed';

	private const INVALID_CASTS = [
		'binary' => null,
		'boolean' => 'bool',
		'double' => 'float',
		'integer' => 'int',
		'real' => 'float',
		'unset' => null,
	];

	/**
	 * @return mixed[]
	 */
	public function register(): array
	{
		return [
			T_STRING_CAST,
			T_BOOL_CAST,
			T_DOUBLE_CAST,
			T_INT_CAST,
			T_UNSET_CAST,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $pointer
	 */
	public function process(\PHP_CodeSniffer\Files\File $phpcsFile, $pointer): void
	{
		$tokens = $phpcsFile->getTokens();
		$cast = $tokens[$pointer]['content'];

		preg_match('~^\(\s*(\S+)\s*\)\z~i', $cast, $matches);

		$castName = $matches[1];
		$castNameLower = strtolower($castName);

		if (!array_key_exists($castNameLower, self::INVALID_CASTS)) {
			return;
		}

		if ($castNameLower === 'unset') {
			$phpcsFile->addError(sprintf('Cast "%s" is forbidden, use "unset(...)" or assign "null" instead.', $cast), $pointer, self::CODE_FORBIDDEN_CAST_USED);

			return;
		}

		if ($castNameLower === 'binary') {
			$fix = $phpcsFile->addFixableError(sprintf('"Cast "%s" is forbidden and has no effect.', $cast), $pointer, self::CODE_FORBIDDEN_CAST_USED);

			if (!$fix) {
				return;
			}

			for ($i = $pointer, $end = TokenHelper::findNextEffective($phpcsFile, $pointer + 1); $i < $end; $i++) {
				$phpcsFile->fixer->beginChangeset();
				$phpcsFile->fixer->replaceToken($i, '');
				$phpcsFile->fixer->endChangeset();
			}

			return;
		}

		$fix = $phpcsFile->addFixableError(
			sprintf('Cast "%s" is forbidden, use "(%s)" instead.', $cast, self::INVALID_CASTS[$castNameLower]),
			$pointer,
			self::CODE_INVALID_CAST_USED
		);

		if (!$fix) {
			return;
		}

		$phpcsFile->fixer->beginChangeset();
		$phpcsFile->fixer->replaceToken($pointer, '(' . self::INVALID_CASTS[$castNameLower] . ')');
		$phpcsFile->fixer->endChangeset();
	}

}
