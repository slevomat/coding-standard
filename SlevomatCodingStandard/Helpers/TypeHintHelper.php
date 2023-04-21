<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

use PHP_CodeSniffer\Files\File;
use SlevomatCodingStandard\Helpers\Annotation\TemplateAnnotation;
use SlevomatCodingStandard\Helpers\Annotation\TypeAliasAnnotation;
use SlevomatCodingStandard\Helpers\Annotation\TypeImportAnnotation;
use function array_key_exists;
use function array_map;
use function array_merge;
use function array_unique;
use function count;
use function implode;
use function in_array;
use function preg_split;
use function sort;
use function sprintf;
use function substr;
use const PREG_SPLIT_DELIM_CAPTURE;
use const T_FUNCTION;
use const T_WHITESPACE;

/**
 * @internal
 */
class TypeHintHelper
{

	public static function isValidTypeHint(
		string $typeHint,
		bool $enableObjectTypeHint,
		bool $enableStaticTypeHint,
		bool $enableMixedTypeHint,
		bool $enableStandaloneNullTrueFalseTypeHints
	): bool
	{
		if (self::isSimpleTypeHint($typeHint)) {
			return true;
		}

		if ($typeHint === 'object') {
			return $enableObjectTypeHint;
		}

		if ($typeHint === 'static') {
			return $enableStaticTypeHint;
		}

		if ($typeHint === 'mixed') {
			return $enableMixedTypeHint;
		}

		if (in_array($typeHint, ['null', 'true', 'false'], true)) {
			return $enableStandaloneNullTrueFalseTypeHints;
		}

		return !self::isSimpleUnofficialTypeHints($typeHint);
	}

	public static function isSimpleTypeHint(string $typeHint): bool
	{
		return in_array($typeHint, self::getSimpleTypeHints(), true);
	}

	public static function isSimpleIterableTypeHint(string $typeHint): bool
	{
		return in_array($typeHint, self::getSimpleIterableTypeHints(), true);
	}

	public static function convertLongSimpleTypeHintToShort(string $typeHint): string
	{
		$longToShort = [
			'integer' => 'int',
			'boolean' => 'bool',
		];
		return array_key_exists($typeHint, $longToShort) ? $longToShort[$typeHint] : $typeHint;
	}

	public static function isUnofficialUnionTypeHint(string $typeHint): bool
	{
		return in_array($typeHint, ['scalar', 'numeric'], true);
	}

	public static function isVoidTypeHint(string $typeHint): bool
	{
		return $typeHint === 'void';
	}

	public static function isNeverTypeHint(string $typeHint): bool
	{
		return in_array($typeHint, ['never', 'never-return', 'never-returns', 'no-return'], true);
	}

	/**
	 * @return list<string>
	 */
	public static function convertUnofficialUnionTypeHintToOfficialTypeHints(string $typeHint): array
	{
		$conversion = [
			'scalar' => ['string', 'int', 'float', 'bool'],
			'numeric' => ['int', 'float'],
		];

		return $conversion[$typeHint];
	}

	public static function isTypeDefinedInAnnotation(File $phpcsFile, int $pointer, string $typeHint): bool
	{
		$docCommentOpenPointer = DocCommentHelper::findDocCommentOpenPointer($phpcsFile, $pointer);

		if ($docCommentOpenPointer === null) {
			return false;
		}

		return self::isTemplate($phpcsFile, $docCommentOpenPointer, $typeHint)
			|| self::isAlias($phpcsFile, $docCommentOpenPointer, $typeHint);
	}

	public static function getFullyQualifiedTypeHint(File $phpcsFile, int $pointer, string $typeHint): string
	{
		if (self::isSimpleTypeHint($typeHint)) {
			return self::convertLongSimpleTypeHintToShort($typeHint);
		}

		return NamespaceHelper::resolveClassName($phpcsFile, $typeHint, $pointer);
	}

	/**
	 * @return list<string>
	 */
	public static function getSimpleTypeHints(): array
	{
		static $simpleTypeHints;

		if ($simpleTypeHints === null) {
			$simpleTypeHints = [
				'int',
				'integer',
				'false',
				'float',
				'string',
				'bool',
				'boolean',
				'callable',
				'self',
				'array',
				'iterable',
				'void',
				'never',
			];
		}

		return $simpleTypeHints;
	}

	/**
	 * @return list<string>
	 */
	public static function getSimpleIterableTypeHints(): array
	{
		return [
			'array',
			'iterable',
		];
	}

	public static function isSimpleUnofficialTypeHints(string $typeHint): bool
	{
		static $simpleUnofficialTypeHints;

		if ($simpleUnofficialTypeHints === null) {
			$simpleUnofficialTypeHints = [
				'null',
				'mixed',
				'scalar',
				'numeric',
				'true',
				'object',
				'resource',
				'static',
				'$this',
				'class-string',
				'trait-string',
				'callable-string',
				'numeric-string',
				'non-empty-string',
				'non-falsy-string',
				'literal-string',
				'array-key',
				'list',
				'empty',
				'positive-int',
				'negative-int',
				'min',
				'max',
			];
		}

		return in_array($typeHint, $simpleUnofficialTypeHints, true);
	}

	/**
	 * @param list<string> $traversableTypeHints
	 */
	public static function isTraversableType(string $type, array $traversableTypeHints): bool
	{
		return self::isSimpleIterableTypeHint($type) || in_array($type, $traversableTypeHints, true);
	}

	public static function typeHintEqualsAnnotation(
		File $phpcsFile,
		int $functionPointer,
		string $typeHint,
		string $typeHintInAnnotation
	): bool
	{
		/** @var list<string> $typeHintParts */
		$typeHintParts = preg_split('~([&|])~', self::normalize($typeHint), -1, PREG_SPLIT_DELIM_CAPTURE);
		/** @var list<string> $typeHintInAnnotationParts */
		$typeHintInAnnotationParts = preg_split('~([&|])~', self::normalize($typeHintInAnnotation), -1, PREG_SPLIT_DELIM_CAPTURE);

		if (count($typeHintParts) !== count($typeHintInAnnotationParts)) {
			return false;
		}

		for ($i = 0; $i < count($typeHintParts); $i++) {
			if (
				(
					$typeHintParts[$i] === '|'
					|| $typeHintParts[$i] === '&'
				)
				&& $typeHintParts[$i] !== $typeHintInAnnotationParts[$i]
			) {
				return false;
			}

			if (self::getFullyQualifiedTypeHint($phpcsFile, $functionPointer, $typeHintParts[$i]) !== self::getFullyQualifiedTypeHint(
				$phpcsFile,
				$functionPointer,
				$typeHintInAnnotationParts[$i]
			)) {
				return false;
			}
		}

		return true;
	}

	public static function getStartPointer(File $phpcsFile, int $endPointer): int
	{
		$previousPointer = TokenHelper::findPreviousExcluding(
			$phpcsFile,
			array_merge([T_WHITESPACE], TokenHelper::getTypeHintTokenCodes()),
			$endPointer - 1
		);
		return TokenHelper::findNextNonWhitespace($phpcsFile, $previousPointer + 1);
	}

	private static function isTemplate(File $phpcsFile, int $docCommentOpenPointer, string $typeHint): bool
	{
		static $templateAnnotationNames = null;
		if ($templateAnnotationNames === null) {
			foreach (['template', 'template-covariant'] as $annotationName) {
				$templateAnnotationNames[] = sprintf('@%s', $annotationName);
				foreach (AnnotationHelper::STATIC_ANALYSIS_PREFIXES as $prefixAnnotationName) {
					$templateAnnotationNames[] = sprintf('@%s-%s', $prefixAnnotationName, $annotationName);
				}
			}
		}

		$containsTypeHintInTemplateAnnotation = static function (int $docCommentOpenPointer) use ($phpcsFile, $templateAnnotationNames, $typeHint): bool {
			$annotations = AnnotationHelper::getAnnotations($phpcsFile, $docCommentOpenPointer);
			foreach ($templateAnnotationNames as $templateAnnotationName) {
				if (!array_key_exists($templateAnnotationName, $annotations)) {
					continue;
				}

				/** @var TemplateAnnotation $templateAnnotation */
				foreach ($annotations[$templateAnnotationName] as $templateAnnotation) {
					if ($templateAnnotation->isInvalid()) {
						continue;
					}

					if ($templateAnnotation->getTemplateName() === $typeHint) {
						return true;
					}
				}
			}

			return false;
		};

		$tokens = $phpcsFile->getTokens();

		$docCommentOwnerPointer = DocCommentHelper::findDocCommentOwnerPointer($phpcsFile, $docCommentOpenPointer);
		if ($docCommentOwnerPointer !== null) {
			if (in_array($tokens[$docCommentOwnerPointer]['code'], TokenHelper::$typeKeywordTokenCodes, true)) {
				return $containsTypeHintInTemplateAnnotation($docCommentOpenPointer);
			}

			if ($tokens[$docCommentOwnerPointer]['code'] === T_FUNCTION && $containsTypeHintInTemplateAnnotation($docCommentOpenPointer)) {
				return true;
			}
		}

		$classPointer = ClassHelper::getClassPointer($phpcsFile, $docCommentOpenPointer);

		if ($classPointer === null) {
			return false;
		}

		$classDocCommentOpenPointer = DocCommentHelper::findDocCommentOpenPointer($phpcsFile, $classPointer);
		if ($classDocCommentOpenPointer === null) {
			return false;
		}

		return $containsTypeHintInTemplateAnnotation($classDocCommentOpenPointer);
	}

	private static function isAlias(File $phpcsFile, int $docCommentOpenPointer, string $typeHint): bool
	{
		static $aliasAnnotationNames = null;
		if ($aliasAnnotationNames === null) {
			foreach (['type', 'import-type'] as $annotationName) {
				foreach (AnnotationHelper::STATIC_ANALYSIS_PREFIXES as $prefixAnnotationName) {
					$aliasAnnotationNames[] = sprintf('@%s-%s', $prefixAnnotationName, $annotationName);
				}
			}
		}

		$classPointer = ClassHelper::getClassPointer($phpcsFile, $docCommentOpenPointer);

		if ($classPointer === null) {
			return false;
		}

		$classDocCommentOpenPointer = DocCommentHelper::findDocCommentOpenPointer($phpcsFile, $classPointer);
		if ($classDocCommentOpenPointer === null) {
			return false;
		}

		$annotations = AnnotationHelper::getAnnotations($phpcsFile, $classDocCommentOpenPointer);
		foreach ($aliasAnnotationNames as $aliasAnnotationName) {
			if (!array_key_exists($aliasAnnotationName, $annotations)) {
				continue;
			}

			/** @var TypeAliasAnnotation|TypeImportAnnotation $aliasAnnotation */
			foreach ($annotations[$aliasAnnotationName] as $aliasAnnotation) {
				if ($aliasAnnotation->isInvalid()) {
					continue;
				}

				if ($aliasAnnotation->getAlias() === $typeHint) {
					return true;
				}
			}
		}

		return false;
	}

	private static function normalize(string $typeHint): string
	{
		if (StringHelper::startsWith($typeHint, '?')) {
			$typeHint = substr($typeHint, 1) . '|null';
		}

		if (self::isNeverTypeHint($typeHint)) {
			return 'never';
		}

		/** @var list<string> $parts */
		$parts = preg_split('~([&|])~', $typeHint, -1, PREG_SPLIT_DELIM_CAPTURE);

		$hints = [];
		$delimiter = '|';
		foreach ($parts as $part) {
			if ($part === '|' || $part === '&') {
				$delimiter = $part;
				continue;
			}

			$hints[] = $part;
		}

		if (in_array('mixed', $hints, true)) {
			return 'mixed';
		}

		$convertedHints = [];
		foreach ($hints as $hint) {
			if (self::isUnofficialUnionTypeHint($hint) && $delimiter !== '&') {
				$convertedHints = array_merge($convertedHints, self::convertUnofficialUnionTypeHintToOfficialTypeHints($hint));
			} else {
				$convertedHints[] = $hint;
			}
		}

		$convertedHints = array_unique($convertedHints);

		if (count($convertedHints) > 1) {
			$convertedHints = array_map(static function (string $part): string {
				return self::isVoidTypeHint($part) ? 'null' : $part;
			}, $convertedHints);
		}

		sort($convertedHints);

		return implode($delimiter, $convertedHints);
	}

}
