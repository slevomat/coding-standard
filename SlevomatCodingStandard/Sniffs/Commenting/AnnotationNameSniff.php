<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Commenting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\AnnotationHelper;
use SlevomatCodingStandard\Helpers\FixerHelper;
use SlevomatCodingStandard\Helpers\NamespaceHelper;
use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use function array_combine;
use function array_key_exists;
use function array_map;
use function array_merge;
use function array_unique;
use function implode;
use function ltrim;
use function preg_match_all;
use function sprintf;
use function strlen;
use function strpos;
use function strtolower;
use function substr;
use const PREG_OFFSET_CAPTURE;
use const T_DOC_COMMENT_OPEN_TAG;

class AnnotationNameSniff implements Sniff
{

	public const CODE_ANNOTATION_NAME_INCORRECT = 'AnnotationNameIncorrect';

	private const STANDARD_ANNOTATIONS = [
		'api',
		'author',
		'category',
		'copyright',
		'deprecated',
		'example',
		'filesource',
		'global',
		'ignore',
		'inheritDoc',
		'internal',
		'license',
		'link',
		'method',
		'package',
		'param',
		'property',
		'property-read',
		'property-write',
		'return',
		'see',
		'since',
		'source',
		'subpackage',
		'throws',
		'todo',
		'uses',
		'used-by',
		'var',
		'version',
	];

	private const STATIC_ANALYSIS_ANNOTATIONS = [
		'api',
		'allow-private-mutation',
		'assert',
		'assert-if-true',
		'assert-if-false',
		'consistent-constructor',
		'consistent-templates',
		'extends',
		'external-mutation-free',
		'implements',
		'mixin',
		'ignore-falsable-return',
		'ignore-nullable-return',
		'ignore-var',
		'ignore-variable-method',
		'ignore-variable-property',
		'immutable',
		'import-type',
		'internal',
		'method',
		'mutation-free',
		'no-named-arguments',
		'param',
		'param-out',
		'property',
		'property-read',
		'property-write',
		'psalm-check-type',
		'psalm-check-type-exact',
		'psalm-suppress',
		'psalm-trace',
		'pure',
		'readonly',
		'readonly-allow-private-mutation',
		'require-extends',
		'require-implements',
		'return',
		'seal-properties',
		'self-out',
		'template',
		'template-covariant',
		'template-extends',
		'template-implements',
		'template-use',
		'this-out',
		'type',
		'var',
		'yield',
	];

	private const PHPUNIT_ANNOTATIONS = [
		'author',
		'after',
		'afterClass',
		'backupGlobals',
		'backupStaticAttributes',
		'before',
		'beforeClass',
		'codeCoverageIgnore',
		'codeCoverageIgnoreStart',
		'codeCoverageIgnoreEnd',
		'covers',
		'coversDefaultClass',
		'coversNothing',
		'dataProvider',
		'depends',
		'doesNotPerformAssertions',
		'group',
		'large',
		'medium',
		'preserveGlobalState',
		'requires',
		'runTestsInSeparateProcesses',
		'runInSeparateProcess',
		'small',
		'test',
		'testdox',
		'testWith',
		'ticket',
		'uses',
	];

	/** @var list<string>|null */
	public $annotations;

	/** @var array<string, string>|null */
	private $normalizedAnnotations;

	/**
	 * @return array<int, (int|string)>
	 */
	public function register(): array
	{
		return [
			T_DOC_COMMENT_OPEN_TAG,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @param int $docCommentOpenPointer
	 */
	public function process(File $phpcsFile, $docCommentOpenPointer): void
	{
		$annotations = AnnotationHelper::getAnnotations($phpcsFile, $docCommentOpenPointer);
		$correctAnnotationNames = $this->getNormalizedAnnotationNames();

		foreach ($annotations as $annotation) {
			$lowerCasedAnnotationName = strtolower($annotation->getName());

			if (!array_key_exists($lowerCasedAnnotationName, $correctAnnotationNames)) {
				continue;
			}

			$correctAnnotationName = $correctAnnotationNames[$lowerCasedAnnotationName];

			if ($correctAnnotationName === $annotation->getName()) {
				continue;
			}

			$annotationNameWithoutAtSign = ltrim($annotation->getName(), '@');
			$fullyQualifiedAnnotationName = NamespaceHelper::resolveClassName(
				$phpcsFile,
				$annotationNameWithoutAtSign,
				$annotation->getStartPointer()
			);

			if (NamespaceHelper::normalizeToCanonicalName($fullyQualifiedAnnotationName) !== $annotationNameWithoutAtSign) {
				continue;
			}

			$fix = $phpcsFile->addFixableError(
				sprintf('Annotation name is incorrect. Expected %s, found %s.', $correctAnnotationName, $annotation->getName()),
				$annotation->getStartPointer(),
				self::CODE_ANNOTATION_NAME_INCORRECT
			);
			if (!$fix) {
				continue;
			}

			$phpcsFile->fixer->beginChangeset();

			$phpcsFile->fixer->replaceToken($annotation->getStartPointer(), $correctAnnotationName);

			$phpcsFile->fixer->endChangeset();
		}

		$tokens = $phpcsFile->getTokens();

		$docCommentContent = TokenHelper::getContent($phpcsFile, $docCommentOpenPointer, $tokens[$docCommentOpenPointer]['comment_closer']);

		if (preg_match_all(
			'~\{(' . implode('|', $correctAnnotationNames) . ')\}~i',
			$docCommentContent,
			$matches,
			PREG_OFFSET_CAPTURE
		) === 0) {
			return;
		}

		foreach ($matches[1] as $match) {
			$correctAnnotationName = $correctAnnotationNames[strtolower($match[0])];

			if ($correctAnnotationName === $match[0]) {
				continue;
			}

			$fix = $phpcsFile->addFixableError(
				sprintf('Annotation name is incorrect. Expected %s, found %s.', $correctAnnotationName, $match[0]),
				$docCommentOpenPointer,
				self::CODE_ANNOTATION_NAME_INCORRECT
			);
			if (!$fix) {
				continue;
			}

			$phpcsFile->fixer->beginChangeset();

			$fixedDocCommentContent = substr($docCommentContent, 0, $match[1]) . $correctAnnotationName . substr(
				$docCommentContent,
				$match[1] + strlen($match[0])
			);

			FixerHelper::change(
				$phpcsFile,
				$docCommentOpenPointer,
				$tokens[$docCommentOpenPointer]['comment_closer'],
				$fixedDocCommentContent
			);

			$phpcsFile->fixer->endChangeset();
		}
	}

	/**
	 * @return array<string, string>
	 */
	private function getNormalizedAnnotationNames(): array
	{
		if ($this->normalizedAnnotations !== null) {
			return $this->normalizedAnnotations;
		}

		if ($this->annotations !== null) {
			$annotationNames = array_map(static function (string $annotationName): string {
				return ltrim($annotationName, '@');
			}, SniffSettingsHelper::normalizeArray($this->annotations));
		} else {
			$annotationNames = array_merge(self::STANDARD_ANNOTATIONS, self::PHPUNIT_ANNOTATIONS, self::STATIC_ANALYSIS_ANNOTATIONS);

			foreach (self::STATIC_ANALYSIS_ANNOTATIONS as $annotationName) {
				if (strpos($annotationName, 'psalm') === 0) {
					continue;
				}

				foreach (AnnotationHelper::STATIC_ANALYSIS_PREFIXES as $prefix) {
					$annotationNames[] = sprintf('%s-%s', $prefix, $annotationName);
				}
			}
		}

		$annotationNames = array_map(static function (string $annotationName): string {
			return '@' . $annotationName;
		}, array_unique($annotationNames));

		$this->normalizedAnnotations = array_combine(array_map(static function (string $annotationName): string {
			return strtolower($annotationName);
		}, $annotationNames), $annotationNames);

		return $this->normalizedAnnotations;
	}

}
