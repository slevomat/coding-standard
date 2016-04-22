<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Typehints;

use SlevomatCodingStandard\Helpers\AnnotationHelper;
use SlevomatCodingStandard\Helpers\DocCommentHelper;
use SlevomatCodingStandard\Helpers\FunctionHelper;
use SlevomatCodingStandard\Helpers\NamespaceHelper;
use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use SlevomatCodingStandard\Helpers\SuppressHelper;
use SlevomatCodingStandard\Helpers\UseStatement;
use SlevomatCodingStandard\Helpers\UseStatementHelper;

class TypeHintDeclarationSniff implements \PHP_CodeSniffer_Sniff
{

	const NAME = 'SlevomatCodingStandard.Typehints.TypeHintDeclaration';

	const MISSING_PARAMETER_TYPE_HINT = 'missingParameterTypeHint';

	const MISSING_RETURN_TYPE_HINT = 'missingReturnTypeHint';

	const USELESS_DOC_COMMENT = 'uselessDocComment';

	public $traversableTypeHints = [];

	public $usefulAnnotations = [];

	private $normalizedTraversableTypeHints;

	private $normalizedUsefulAnnotations;

	/**
	 * @return int[]
	 */
	public function register(): array
	{
		return [
			T_FUNCTION,
		];
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.Typehints.TypeHintDeclaration.missingParameterTypeHint
	 * @param \PHP_CodeSniffer_File $phpcsFile
	 * @param int $functionPointer
	 */
	public function process(\PHP_CodeSniffer_File $phpcsFile, $functionPointer)
	{
		$this->checkParametersTypeHints($phpcsFile, $functionPointer);
		$this->checkReturnTypeHints($phpcsFile, $functionPointer);
		$this->checkUselessDocComment($phpcsFile, $functionPointer);
	}

	private function checkParametersTypeHints(\PHP_CodeSniffer_File $phpcsFile, int $functionPointer)
	{
		if (SuppressHelper::isSniffSuppressed($phpcsFile, $functionPointer, $this->getSniffName(self::MISSING_PARAMETER_TYPE_HINT))) {
			return;
		}

		$parametersWithoutTypeHint = FunctionHelper::getParametersWithoutTypeHint($phpcsFile, $functionPointer);
		if (count($parametersWithoutTypeHint) === 0) {
			return;
		}

		$parametersTypeHintsDefinitions = [];
		foreach (FunctionHelper::getParametersAnnotations($phpcsFile, $functionPointer) as $parameterAnnotationContent) {
			list($parameterTypeHintDefinition, $parameterName) = preg_split('~\\s+~', $parameterAnnotationContent);
			$parameterName = preg_replace('~^\.{3}\\s*(\$.+)~', '\\1', $parameterName);
			$parametersTypeHintsDefinitions[$parameterName] = $parameterTypeHintDefinition;
		}

		foreach ($parametersWithoutTypeHint as $parameterName) {
			if (!isset($parametersTypeHintsDefinitions[$parameterName])) {
				$phpcsFile->addError(
					sprintf(
						'%s %s() does not have parameter type hint nor @param annotation for its parameter %s.',
						$this->getFunctionTypeLabel($phpcsFile, $functionPointer),
						FunctionHelper::getFullyQualifiedName($phpcsFile, $functionPointer),
						$parameterName
					),
					$functionPointer,
					self::MISSING_PARAMETER_TYPE_HINT
				);

				continue;
			}

			$parameterTypeHintDefinition = $parametersTypeHintsDefinitions[$parameterName];

			if ($this->definitionContainsMixedTypeHint($parameterTypeHintDefinition) || strtolower($parameterTypeHintDefinition) === 'null') {
				continue;
			}

			if ($this->definitionContainsOneTypeHint($parameterTypeHintDefinition)) {
				$phpcsFile->addError(
					sprintf(
						'%s %s() does not have parameter type hint for its parameter %s but it should be possible to add it based on @param annotation "%s".',
						$this->getFunctionTypeLabel($phpcsFile, $functionPointer),
						FunctionHelper::getFullyQualifiedName($phpcsFile, $functionPointer),
						$parameterName,
						$parameterTypeHintDefinition
					),
					$functionPointer,
					self::MISSING_PARAMETER_TYPE_HINT
				);
			} elseif ($this->definitionContainsJustTwoTypeHints($parameterTypeHintDefinition)) {
				$parameterTypeHints = explode('|', $parameterTypeHintDefinition);
				if (strtolower($parameterTypeHints[0]) === 'null' || strtolower($parameterTypeHints[1]) === 'null') {
					$parameterTypeHint = strtolower($parameterTypeHints[0]) === 'null' ? $parameterTypeHints[1] : $parameterTypeHints[0];
					if ($this->definitionContainsOneTypeHint($parameterTypeHint)) {
						$phpcsFile->addError(
							sprintf(
								'%s %s() does not have parameter type hint for its parameter %s but it should be possible to add it based on @param annotation "%s".',
								$this->getFunctionTypeLabel($phpcsFile, $functionPointer),
								FunctionHelper::getFullyQualifiedName($phpcsFile, $functionPointer),
								$parameterName,
								$parameterTypeHintDefinition
							),
							$functionPointer,
							self::MISSING_PARAMETER_TYPE_HINT
						);
					}
				} elseif ($this->definitionContainsTraversableTypeHint($phpcsFile, $functionPointer, $parameterTypeHintDefinition)) {
					$phpcsFile->addError(
						sprintf(
							'%s %s() does not have parameter type hint for its parameter %s but it should be possible to add it based on @param annotation "%s".',
							$this->getFunctionTypeLabel($phpcsFile, $functionPointer),
							FunctionHelper::getFullyQualifiedName($phpcsFile, $functionPointer),
							$parameterName,
							$parameterTypeHintDefinition
						),
						$functionPointer,
						self::MISSING_PARAMETER_TYPE_HINT
					);
				}
			}
		}
	}

	private function checkReturnTypeHints(\PHP_CodeSniffer_File $phpcsFile, int $functionPointer)
	{
		if (SuppressHelper::isSniffSuppressed($phpcsFile, $functionPointer, $this->getSniffName(self::MISSING_RETURN_TYPE_HINT))) {
			return;
		}

		if (FunctionHelper::isAbstract($phpcsFile, $functionPointer)) {
			return;
		}

		if (!FunctionHelper::returnsValue($phpcsFile, $functionPointer)) {
			return;
		}

		if (FunctionHelper::hasReturnTypeHint($phpcsFile, $functionPointer)) {
			return;
		}

		$returnAnnotation = FunctionHelper::findReturnAnnotation($phpcsFile, $functionPointer);
		if ($returnAnnotation === null) {
			$phpcsFile->addError(
				sprintf(
					'%s %s() does not have return type hint nor @return annotation for its return value.',
					$this->getFunctionTypeLabel($phpcsFile, $functionPointer),
					FunctionHelper::getFullyQualifiedName($phpcsFile, $functionPointer)
				),
				$functionPointer,
				self::MISSING_RETURN_TYPE_HINT
			);

			return;
		}

		$returnTypeHintDefinition = preg_split('~\\s+~', $returnAnnotation)[0];

		if ($this->definitionContainsMixedTypeHint($returnTypeHintDefinition)) {
			return;
		} elseif ($this->definitionContainsNullTypeHint($returnTypeHintDefinition)) {
			return;
		} elseif ($this->definitionContainsOneTypeHint($returnTypeHintDefinition)) {
			$phpcsFile->addError(
				sprintf(
					'%s %s() does not have return type hint for its return value but it should be possible to add it based on @return annotation "%s".',
					$this->getFunctionTypeLabel($phpcsFile, $functionPointer),
					FunctionHelper::getFullyQualifiedName($phpcsFile, $functionPointer),
					$returnTypeHintDefinition
				),
				$functionPointer,
				self::MISSING_RETURN_TYPE_HINT
			);
		} elseif ($this->definitionContainsJustTwoTypeHints($returnTypeHintDefinition) && $this->definitionContainsTraversableTypeHint($phpcsFile, $functionPointer, $returnTypeHintDefinition)) {
			$phpcsFile->addError(
				sprintf(
					'%s %s() does not have return type hint for its return value but it should be possible to add it based on @return annotation "%s".',
					$this->getFunctionTypeLabel($phpcsFile, $functionPointer),
					FunctionHelper::getFullyQualifiedName($phpcsFile, $functionPointer),
					$returnTypeHintDefinition
				),
				$functionPointer,
				self::MISSING_RETURN_TYPE_HINT
			);
		}
	}

	private function checkUselessDocComment(\PHP_CodeSniffer_File $phpcsFile, int $functionPointer)
	{
		if (SuppressHelper::isSniffSuppressed($phpcsFile, $functionPointer, $this->getSniffName(self::USELESS_DOC_COMMENT))) {
			return;
		}

		if (!DocCommentHelper::hasDocComment($phpcsFile, $functionPointer)) {
			return;
		}

		if (DocCommentHelper::hasDocCommentDescription($phpcsFile, $functionPointer)) {
			return;
		}

		$returnTypeHint = FunctionHelper::findReturnTypeHint($phpcsFile, $functionPointer);
		if (FunctionHelper::isAbstract($phpcsFile, $functionPointer)) {
			$returnAnnotation = FunctionHelper::findReturnAnnotation($phpcsFile, $functionPointer);
			if (
				($returnTypeHint !== null && $this->isTraversableTypeHint($this->getFullyQualifiedTypeHint($phpcsFile, $functionPointer, $returnTypeHint)))
				|| ($returnAnnotation !== null && ($this->definitionContainsMixedTypeHint($returnAnnotation) || $this->definitionContainsNullTypeHint($returnAnnotation) || !$this->definitionContainsOneTypeHint($returnAnnotation)))
			) {
				return;
			}
		} else {
			if (
				FunctionHelper::returnsValue($phpcsFile, $functionPointer)
				&& ($returnTypeHint === null || $this->isTraversableTypeHint($this->getFullyQualifiedTypeHint($phpcsFile, $functionPointer, $returnTypeHint)))
			) {
				return;
			}
		}

		foreach (FunctionHelper::getParametersTypeHints($phpcsFile, $functionPointer) as $parameterTypeHint) {
			if ($parameterTypeHint === null || $this->isTraversableTypeHint($this->getFullyQualifiedTypeHint($phpcsFile, $functionPointer, $parameterTypeHint))) {
				return;
			}
		}

		foreach (array_keys(AnnotationHelper::getAnnotations($phpcsFile, $functionPointer)) as $annotationName) {
			if (array_key_exists($annotationName, $this->getNormalizedUsefulAnnotations())) {
				return;
			}
		}

		$phpcsFile->addError(
			sprintf(
				'%s %s() does not need documentation comment.',
				$this->getFunctionTypeLabel($phpcsFile, $functionPointer),
				FunctionHelper::getFullyQualifiedName($phpcsFile, $functionPointer)
			),
			$functionPointer,
			self::USELESS_DOC_COMMENT
		);
	}

	private function getSniffName(string $sniffName): string
	{
		return sprintf('%s.%s', self::NAME, $sniffName);
	}

	private function getFullyQualifiedTypeHint(\PHP_CodeSniffer_File $phpcsFile, int $functionPointer, string $typeHint): string
	{
		if (in_array($typeHint, $this->getSimpleTypeHints(), true) || NamespaceHelper::isFullyQualifiedName($typeHint)) {
			return $typeHint;
		}

		$canonicalQualifiedTypeHint = $typeHint;

		$useStatements = UseStatementHelper::getUseStatements($phpcsFile, $phpcsFile->findPrevious(T_OPEN_TAG, $functionPointer));
		$normalizedTypeHint = UseStatement::normalizedNameAsReferencedInFile($typeHint);
		if (isset($useStatements[$normalizedTypeHint])) {
			$useStatement = $useStatements[$normalizedTypeHint];
			$canonicalQualifiedTypeHint = $useStatement->getFullyQualifiedTypeName();
		} else {
			$fileNamespace = NamespaceHelper::findCurrentNamespaceName($phpcsFile, $functionPointer);
			if ($fileNamespace !== null) {
				$canonicalQualifiedTypeHint = sprintf('%s%s%s', $fileNamespace, NamespaceHelper::NAMESPACE_SEPARATOR, $typeHint);
			}
		}

		return sprintf('%s%s', NamespaceHelper::NAMESPACE_SEPARATOR, $canonicalQualifiedTypeHint);
	}

	private function definitionContainsMixedTypeHint(string $typeHintDefinition): bool
	{
		return preg_match('~(?:^mixed$)|(?:^mixed\|)|(\|mixed\|)|(?:\|mixed$)~i', $typeHintDefinition) !== 0;
	}

	private function definitionContainsNullTypeHint(string $typeHintDefinition): bool
	{
		return preg_match('~(?:^null$)|(?:^null\|)|(\|null\|)|(?:\|null$)~i', $typeHintDefinition) !== 0;
	}

	private function definitionContainsOneTypeHint(string $typeHintDefinition): bool
	{
		return preg_match(sprintf('~^(?:%s|(\\\\\\w+)+)(?:\[\])?$~i', implode('|', $this->getSimpleTypeHints())), $typeHintDefinition) !== 0;
	}

	private function definitionContainsJustTwoTypeHints(string $typeHintDefinition): bool
	{
		return count(explode('|', $typeHintDefinition)) === 2;
	}

	private function definitionContainsTraversableTypeHint(\PHP_CodeSniffer_File $phpcsFile, int $functionPointer, string $typeHintDefinition): bool
	{
		if (!preg_match('~\[\](?:\||$)~', $typeHintDefinition)) {
			return false;
		}

		return array_reduce(explode('|', $typeHintDefinition), function ($carry, $typeHint) use ($phpcsFile, $functionPointer) {
			$fullyQualifiedTypeHint = $this->getFullyQualifiedTypeHint($phpcsFile, $functionPointer, $typeHint);
			if ($this->isTraversableTypeHint($fullyQualifiedTypeHint)) {
				$carry = true;
			}
			return $carry;
		}, false);
	}

	private function isTraversableTypeHint(string $typeHint): bool
	{
		return strtolower($typeHint) === 'array' || array_key_exists($typeHint, $this->getNormalizedTraversableTypeHints());
	}

	/**
	 * @return string[]
	 */
	private function getSimpleTypeHints(): array
	{
		static $simpleTypeHints = [
			'int',
			'integer',
			'float',
			'string',
			'bool',
			'boolean',
			'callable',
			'self',
			'array',
		];

		return $simpleTypeHints;
	}

	/**
	 * @return int[] [string => int]
	 */
	private function getNormalizedTraversableTypeHints(): array
	{
		if ($this->normalizedTraversableTypeHints === null) {
			$this->normalizedTraversableTypeHints = array_flip(array_map(function ($typeHint) {
				return NamespaceHelper::isFullyQualifiedName($typeHint) ? $typeHint : sprintf('%s%s', NamespaceHelper::NAMESPACE_SEPARATOR, $typeHint);
			}, SniffSettingsHelper::normalizeArray($this->traversableTypeHints)));
		}
		return $this->normalizedTraversableTypeHints;
	}

	/**
	 * @return int[] [string => int]
	 */
	private function getNormalizedUsefulAnnotations(): array
	{
		if ($this->normalizedUsefulAnnotations === null) {
			$this->normalizedUsefulAnnotations = array_flip(SniffSettingsHelper::normalizeArray($this->usefulAnnotations));
		}
		return $this->normalizedUsefulAnnotations;
	}

	private function getFunctionTypeLabel(\PHP_CodeSniffer_File $phpcsFile, int $functionPointer): string
	{
		return FunctionHelper::isMethod($phpcsFile, $functionPointer) ? 'Method' : 'Function';
	}

}
