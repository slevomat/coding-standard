<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\TypeHints;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use SlevomatCodingStandard\Helpers\AnnotationHelper;
use SlevomatCodingStandard\Helpers\AnnotationTypeHelper;
use SlevomatCodingStandard\Helpers\DocCommentHelper;
use SlevomatCodingStandard\Helpers\NamespaceHelper;
use SlevomatCodingStandard\Helpers\PropertyHelper;
use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use SlevomatCodingStandard\Helpers\SuppressHelper;
use function array_map;
use function count;
use function sprintf;
use const T_VARIABLE;

class PropertyTypeHintSniff implements Sniff
{

	private const NAME = 'SlevomatCodingStandard.TypeHints.PropertyTypeHint';

	public const CODE_MISSING_ANY_TYPE_HINT = 'MissingAnyTypeHint';

	public const CODE_MISSING_TRAVERSABLE_TYPE_HINT_SPECIFICATION = 'MissingTraversableTypeHintSpecification';

	/** @var string[] */
	public $traversableTypeHints = [];

	/** @var array<int, string>|null */
	private $normalizedTraversableTypeHints;

	/**
	 * @return (int|string)[]
	 */
	public function register(): array
	{
		return [
			T_VARIABLE,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $propertyPointer
	 */
	public function process(File $phpcsFile, $propertyPointer): void
	{
		if (!PropertyHelper::isProperty($phpcsFile, $propertyPointer)) {
			return;
		}

		if (SuppressHelper::isSniffSuppressed($phpcsFile, $propertyPointer, self::NAME)) {
			return;
		}

		if (DocCommentHelper::hasInheritdocAnnotation($phpcsFile, $propertyPointer)) {
			return;
		}

		/** @var \SlevomatCodingStandard\Helpers\Annotation\VariableAnnotation[] $varAnnotations */
		$varAnnotations = AnnotationHelper::getAnnotationsByName($phpcsFile, $propertyPointer, '@var');

		if (count($varAnnotations) === 0) {
			if (!SuppressHelper::isSniffSuppressed($phpcsFile, $propertyPointer, $this->getSniffName(self::CODE_MISSING_ANY_TYPE_HINT))) {
				$phpcsFile->addError(
					sprintf(
						'Property %s does not have @var annotation.',
						PropertyHelper::getFullyQualifiedName($phpcsFile, $propertyPointer)
					),
					$propertyPointer,
					self::CODE_MISSING_ANY_TYPE_HINT
				);
			}

			return;
		}

		if (SuppressHelper::isSniffSuppressed($phpcsFile, $propertyPointer, $this->getSniffName(self::CODE_MISSING_TRAVERSABLE_TYPE_HINT_SPECIFICATION))) {
			return;
		}

		if ($varAnnotations[0]->isInvalid()) {
			return;
		}

		$propertyTypeNode = $varAnnotations[0]->getType();

		if (
			!AnnotationTypeHelper::containsTraversableType($propertyTypeNode, $phpcsFile, $propertyPointer, $this->getTraversableTypeHints())
			|| AnnotationTypeHelper::containsItemsSpecificationForTraversable($propertyTypeNode, $phpcsFile, $propertyPointer, $this->getTraversableTypeHints())
		) {
			return;
		}

		$phpcsFile->addError(
			sprintf(
				'@var annotation of property %s does not specify type hint for its items.',
				PropertyHelper::getFullyQualifiedName($phpcsFile, $propertyPointer)
			),
			$varAnnotations[0]->getStartPointer(),
			self::CODE_MISSING_TRAVERSABLE_TYPE_HINT_SPECIFICATION
		);
	}

	private function getSniffName(string $sniffName): string
	{
		return sprintf('%s.%s', self::NAME, $sniffName);
	}

	/**
	 * @return array<int, string>
	 */
	private function getTraversableTypeHints(): array
	{
		if ($this->normalizedTraversableTypeHints === null) {
			$this->normalizedTraversableTypeHints = array_map(static function (string $typeHint): string {
				return NamespaceHelper::isFullyQualifiedName($typeHint) ? $typeHint : sprintf('%s%s', NamespaceHelper::NAMESPACE_SEPARATOR, $typeHint);
			}, SniffSettingsHelper::normalizeArray($this->traversableTypeHints));
		}
		return $this->normalizedTraversableTypeHints;
	}

}
