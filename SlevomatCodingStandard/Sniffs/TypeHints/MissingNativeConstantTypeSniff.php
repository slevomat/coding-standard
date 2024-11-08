<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\TypeHints;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\ClassHelper;
use SlevomatCodingStandard\Helpers\NamespaceHelper;
use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use SlevomatCodingStandard\Helpers\SuppressHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function array_keys;
use function count;
use function enum_exists;
use function in_array;
use function sprintf;
use const T_CONST;
use const T_EQUAL;
use const T_FALSE;
use const T_MINUS;
use const T_NULL;
use const T_OPEN_SHORT_ARRAY;
use const T_TRUE;

class MissingNativeConstantTypeSniff implements Sniff
{

	public const CODE_MISSING_CONSTANT_TYPE = 'MissingConstantType';
	private const NAME = 'SlevomatCodingStandard.TypeHints.MissingConstantType';

	private const T_LNUMBER = 311;
	private const T_DNUMBER = 312;
	private const T_STRING = 313;
	private const T_CONSTANT_ENCAPSED_STRING = 320;

	private const TOKEN_TO_TYPE_MAP = [
		self::T_DNUMBER => 'float',
		self::T_LNUMBER => 'int',
		T_NULL => 'null',
		T_TRUE => 'true',
		T_FALSE => 'false',
		T_OPEN_SHORT_ARRAY => 'array',
		self::T_CONSTANT_ENCAPSED_STRING => 'string',
	];

	/** @var bool */
	public $enable = true;

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return [
			T_CONST,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @param int $stackPtr
	 */
	public function process(File $phpcsFile, $stackPtr): void
	{
		$this->enable = SniffSettingsHelper::isEnabledByPhpVersion($this->enable, 80300);

		if (!$this->enable) {
			return;
		}

		if (SuppressHelper::isSniffSuppressed($phpcsFile, $stackPtr, self::NAME)) {
			return;
		}

		$tokens = $phpcsFile->getTokens();

		/** @var int $classPointer */
		$classPointer = array_keys($tokens[$stackPtr]['conditions'])[count($tokens[$stackPtr]['conditions']) - 1];
		$typePointer = TokenHelper::findNextEffective($phpcsFile, $stackPtr + 1);
		if (in_array($tokens[$typePointer]['code'], [T_NULL, T_TRUE, T_FALSE], true)) {
			return;
		}

		if (
			$tokens[$typePointer]['code'] === self::T_STRING
			&& in_array($tokens[$typePointer]['content'], ['int', 'string', 'float', 'double', 'array', 'object'], true)
		) {
			return;
		}

		$equalSignPointer = TokenHelper::findNext($phpcsFile, T_EQUAL, $stackPtr + 1);
		$namePointer = TokenHelper::findPreviousEffective($phpcsFile, $equalSignPointer - 1);

		if (
			$tokens[$typePointer]['code'] === self::T_STRING
			&& $namePointer !== $typePointer
		) {
			$className = NamespaceHelper::resolveClassName($phpcsFile, $tokens[$typePointer]['content'], $typePointer);
			if (enum_exists($className)) {
				return;
			}
		}

		$assignedValuePointer = TokenHelper::findNextEffective($phpcsFile, $equalSignPointer + 1);
		if ($tokens[$assignedValuePointer]['code'] === T_MINUS) {
			$assignedValuePointer = TokenHelper::findNextEffective($phpcsFile, $assignedValuePointer + 1);
		}

		$fixableType = self::TOKEN_TO_TYPE_MAP[$tokens[$assignedValuePointer]['code']] ?? null;
		if ($fixableType === null) {
			$className = NamespaceHelper::resolveClassName($phpcsFile, $tokens[$assignedValuePointer]['content'], $assignedValuePointer);
			if (enum_exists($className)) {
				$fixableType = $tokens[$assignedValuePointer]['content'];
			}
		}

		if ($fixableType !== null) {
			$message = sprintf(
				'Constant %s::%s is missing a type (%s).',
				ClassHelper::getFullyQualifiedName($phpcsFile, $classPointer),
				$tokens[$namePointer]['content'],
				$fixableType
			);

			$fix = $phpcsFile->addFixableError($message, $typePointer, self::CODE_MISSING_CONSTANT_TYPE);
			if ($fix) {
				$phpcsFile->fixer->beginChangeset();
				$phpcsFile->fixer->addContentBefore($typePointer, $fixableType . ' ');
				$phpcsFile->fixer->endChangeset();
			}

			return;
		}

		$message = sprintf(
			'Constant %s::%s is missing a type.',
			ClassHelper::getFullyQualifiedName($phpcsFile, $classPointer),
			$tokens[$namePointer]['content']
		);

		$phpcsFile->addError($message, $stackPtr, self::CODE_MISSING_CONSTANT_TYPE);
	}

}
