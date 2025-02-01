<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\TypeHints;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\AnnotationHelper;
use SlevomatCodingStandard\Helpers\ClassHelper;
use SlevomatCodingStandard\Helpers\DocCommentHelper;
use SlevomatCodingStandard\Helpers\FixerHelper;
use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function array_key_exists;
use function count;
use function sprintf;
use const T_CONST;
use const T_CONSTANT_ENCAPSED_STRING;
use const T_DNUMBER;
use const T_DOC_COMMENT_WHITESPACE;
use const T_EQUAL;
use const T_FALSE;
use const T_LNUMBER;
use const T_MINUS;
use const T_NULL;
use const T_OPEN_SHORT_ARRAY;
use const T_START_HEREDOC;
use const T_START_NOWDOC;
use const T_TRUE;

class ClassConstantTypeHintSniff implements Sniff
{

	public const CODE_MISSING_NATIVE_TYPE_HINT = 'MissingNativeTypeHint';
	public const CODE_USELESS_DOC_COMMENT = 'UselessDocComment';
	public const CODE_USELESS_VAR_ANNOTATION = 'UselessVarAnnotation';

	public ?bool $enableNativeTypeHint = null;

	/** @var array<int|string, string> */
	private static array $tokenToTypeHintMapping = [
		T_FALSE => 'false',
		T_TRUE => 'true',
		T_DNUMBER => 'float',
		T_LNUMBER => 'int',
		T_NULL => 'null',
		T_OPEN_SHORT_ARRAY => 'array',
		T_CONSTANT_ENCAPSED_STRING => 'string',
		T_START_NOWDOC => 'string',
		T_START_HEREDOC => 'string',
	];

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
	 * @param int $constantPointer
	 */
	public function process(File $phpcsFile, $constantPointer): void
	{
		if (ClassHelper::getClassPointer($phpcsFile, $constantPointer) === null) {
			// Constant in namespace
			return;
		}

		$this->checkNativeTypeHint($phpcsFile, $constantPointer);
		$this->checkDocComment($phpcsFile, $constantPointer);
	}

	private function checkNativeTypeHint(File $phpcsFile, int $constantPointer): void
	{
		$this->enableNativeTypeHint = SniffSettingsHelper::isEnabledByPhpVersion($this->enableNativeTypeHint, 80300);

		if (!$this->enableNativeTypeHint) {
			return;
		}

		$namePointer = $this->getConstantNamePointer($phpcsFile, $constantPointer);
		$typeHintPointer = TokenHelper::findPreviousEffective($phpcsFile, $namePointer - 1);

		if ($typeHintPointer !== $constantPointer) {
			// Has type hint
			return;
		}

		$tokens = $phpcsFile->getTokens();

		$namePointer = $this->getConstantNamePointer($phpcsFile, $constantPointer);
		$equalPointer = TokenHelper::findNext($phpcsFile, T_EQUAL, $constantPointer + 1);

		$valuePointer = TokenHelper::findNextEffective($phpcsFile, $equalPointer + 1);
		if ($tokens[$valuePointer]['code'] === T_MINUS) {
			$valuePointer = TokenHelper::findNextEffective($phpcsFile, $valuePointer + 1);
		}

		$constantName = $tokens[$namePointer]['content'];

		$typeHint = null;
		if (array_key_exists($tokens[$valuePointer]['code'], self::$tokenToTypeHintMapping)) {
			$typeHint = self::$tokenToTypeHintMapping[$tokens[$valuePointer]['code']];
		}

		$errorParameters = [
			sprintf('Constant %s does not have native type hint.', $constantName),
			$constantPointer,
			self::CODE_MISSING_NATIVE_TYPE_HINT,
		];

		if ($typeHint === null) {
			$phpcsFile->addError(...$errorParameters);
			return;
		}

		$fix = $phpcsFile->addFixableError(...$errorParameters);

		if (!$fix) {
			return;
		}

		$phpcsFile->fixer->beginChangeset();
		$phpcsFile->fixer->addContent($constantPointer, ' ' . $typeHint);
		$phpcsFile->fixer->endChangeset();
	}

	private function checkDocComment(File $phpcsFile, int $constantPointer): void
	{
		$docCommentOpenPointer = DocCommentHelper::findDocCommentOpenPointer($phpcsFile, $constantPointer);
		if ($docCommentOpenPointer === null) {
			return;
		}

		$annotations = AnnotationHelper::getAnnotations($phpcsFile, $constantPointer, '@var');

		if ($annotations === []) {
			return;
		}

		$tokens = $phpcsFile->getTokens();

		$namePointer = $this->getConstantNamePointer($phpcsFile, $constantPointer);
		$constantName = $tokens[$namePointer]['content'];

		$uselessDocComment = !DocCommentHelper::hasDocCommentDescription($phpcsFile, $constantPointer) && count($annotations) === 1;
		if ($uselessDocComment) {
			$fix = $phpcsFile->addFixableError(
				sprintf('Useless documentation comment for constant %s.', $constantName),
				$docCommentOpenPointer,
				self::CODE_USELESS_DOC_COMMENT,
			);

			/** @var int $fixerStart */
			$fixerStart = TokenHelper::findLastTokenOnPreviousLine($phpcsFile, $docCommentOpenPointer);
			$fixerEnd = $tokens[$docCommentOpenPointer]['comment_closer'];
		} else {
			$annotation = $annotations[0];

			$fix = $phpcsFile->addFixableError(
				sprintf('Useless @var annotation for constant %s.', $constantName),
				$annotation->getStartPointer(),
				self::CODE_USELESS_VAR_ANNOTATION,
			);

			/** @var int $fixerStart */
			$fixerStart = TokenHelper::findPreviousContent(
				$phpcsFile,
				T_DOC_COMMENT_WHITESPACE,
				$phpcsFile->eolChar,
				$annotation->getStartPointer() - 1,
			);
			$fixerEnd = $annotation->getEndPointer();
		}

		if (!$fix) {
			return;
		}

		$phpcsFile->fixer->beginChangeset();
		FixerHelper::removeBetweenIncluding($phpcsFile, $fixerStart, $fixerEnd);
		$phpcsFile->fixer->endChangeset();
	}

	private function getConstantNamePointer(File $phpcsFile, int $constantPointer): int
	{
		$equalPointer = TokenHelper::findNext($phpcsFile, T_EQUAL, $constantPointer + 1);

		return TokenHelper::findPreviousEffective($phpcsFile, $equalPointer - 1);
	}

}
