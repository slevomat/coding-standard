<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Commenting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\AnnotationHelper;
use SlevomatCodingStandard\Helpers\DocCommentHelper;
use SlevomatCodingStandard\Helpers\FixerHelper;
use SlevomatCodingStandard\Helpers\FunctionHelper;
use SlevomatCodingStandard\Helpers\NamespaceHelper;
use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function array_key_exists;
use function array_map;
use function in_array;
use function sprintf;
use const T_FUNCTION;

class UselessFunctionDocCommentSniff implements Sniff
{

	public const CODE_USELESS_DOC_COMMENT = 'UselessDocComment';

	/** @var list<string> */
	public $traversableTypeHints = [];

	/** @var array<int, string>|null */
	private $normalizedTraversableTypeHints;

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return [
			T_FUNCTION,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @param int $functionPointer
	 */
	public function process(File $phpcsFile, $functionPointer): void
	{
		if (!DocCommentHelper::hasDocComment($phpcsFile, $functionPointer)) {
			return;
		}

		if (DocCommentHelper::hasInheritdocAnnotation($phpcsFile, $functionPointer)) {
			return;
		}

		if (DocCommentHelper::hasDocCommentDescription($phpcsFile, $functionPointer)) {
			return;
		}

		$returnTypeHint = FunctionHelper::findReturnTypeHint($phpcsFile, $functionPointer);
		$returnAnnotation = FunctionHelper::findReturnAnnotation($phpcsFile, $functionPointer);

		if (
			$returnAnnotation !== null
			&& !AnnotationHelper::isAnnotationUseless(
				$phpcsFile,
				$functionPointer,
				$returnTypeHint,
				$returnAnnotation,
				$this->getTraversableTypeHints()
			)
		) {
			return;
		}

		$parameterTypeHints = FunctionHelper::getParametersTypeHints($phpcsFile, $functionPointer);
		$parametersAnnotations = FunctionHelper::getValidParametersAnnotations($phpcsFile, $functionPointer);

		foreach ($parametersAnnotations as $parameterName => $parameterAnnotation) {
			if (!array_key_exists($parameterName, $parameterTypeHints)) {
				return;
			}

			if (!AnnotationHelper::isAnnotationUseless(
				$phpcsFile,
				$functionPointer,
				$parameterTypeHints[$parameterName],
				$parameterAnnotation,
				$this->getTraversableTypeHints()
			)) {
				return;
			}
		}

		foreach (AnnotationHelper::getAnnotations($phpcsFile, $functionPointer) as $annotation) {
			if (!in_array($annotation->getName(), ['@param', '@return'], true)) {
				return;
			}
		}

		$fix = $phpcsFile->addFixableError(
			sprintf(
				'%s %s() does not need documentation comment.',
				FunctionHelper::getTypeLabel($phpcsFile, $functionPointer),
				FunctionHelper::getFullyQualifiedName($phpcsFile, $functionPointer)
			),
			$functionPointer,
			self::CODE_USELESS_DOC_COMMENT
		);
		if (!$fix) {
			return;
		}

		/** @var int $docCommentOpenPointer */
		$docCommentOpenPointer = DocCommentHelper::findDocCommentOpenPointer($phpcsFile, $functionPointer);
		$docCommentClosePointer = $phpcsFile->getTokens()[$docCommentOpenPointer]['comment_closer'];

		$changeStart = $docCommentOpenPointer;
		/** @var int $changeEnd */
		$changeEnd = TokenHelper::findNextEffective($phpcsFile, $docCommentClosePointer + 1) - 1;

		$phpcsFile->fixer->beginChangeset();

		FixerHelper::removeBetweenIncluding($phpcsFile, $changeStart, $changeEnd);

		$phpcsFile->fixer->endChangeset();
	}

	/**
	 * @return array<int, string>
	 */
	private function getTraversableTypeHints(): array
	{
		if ($this->normalizedTraversableTypeHints === null) {
			$this->normalizedTraversableTypeHints = array_map(static function (string $typeHint): string {
				return NamespaceHelper::isFullyQualifiedName($typeHint)
					? $typeHint
					: sprintf('%s%s', NamespaceHelper::NAMESPACE_SEPARATOR, $typeHint);
			}, SniffSettingsHelper::normalizeArray($this->traversableTypeHints));
		}
		return $this->normalizedTraversableTypeHints;
	}

}
