<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

use SlevomatCodingStandard\Helpers\AnnotationHelper;
use SlevomatCodingStandard\Helpers\ClassHelper;
use SlevomatCodingStandard\Helpers\FunctionHelper;
use SlevomatCodingStandard\Helpers\NamespaceHelper;
use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use SlevomatCodingStandard\Helpers\StringHelper;
use SlevomatCodingStandard\Helpers\SuppressHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;
use SlevomatCodingStandard\Helpers\UseStatementHelper;

class UnusedPrivateElementsSniff implements \PHP_CodeSniffer\Sniffs\Sniff
{

	private const NAME = 'SlevomatCodingStandard.Classes.UnusedPrivateElements';

	public const CODE_UNUSED_PROPERTY = 'UnusedProperty';

	public const CODE_WRITE_ONLY_PROPERTY = 'WriteOnlyProperty';

	public const CODE_UNUSED_METHOD = 'UnusedMethod';

	public const CODE_UNUSED_CONSTANT = 'UnusedConstant';

	/** @var string[] */
	public $alwaysUsedPropertiesAnnotations = [];

	/** @var string[]|null */
	private $normalizedAlwaysUsedPropertiesAnnotations;

	/** @var string[] */
	public $alwaysUsedPropertiesSuffixes = [];

	/** @var string[]|null */
	private $normalizedAlwaysUsedPropertiesSuffixes;

	/**
	 * @return mixed[]
	 */
	public function register(): array
	{
		return [
			T_CLASS,
		];
	}

	/**
	 * @return string[]
	 */
	private function getAlwaysUsedPropertiesAnnotations(): array
	{
		if ($this->normalizedAlwaysUsedPropertiesAnnotations === null) {
			$this->normalizedAlwaysUsedPropertiesAnnotations = SniffSettingsHelper::normalizeArray($this->alwaysUsedPropertiesAnnotations);
		}

		return $this->normalizedAlwaysUsedPropertiesAnnotations;
	}

	/**
	 * @return string[]
	 */
	private function getAlwaysUsedPropertiesSuffixes(): array
	{
		if ($this->normalizedAlwaysUsedPropertiesSuffixes === null) {
			$this->normalizedAlwaysUsedPropertiesSuffixes = SniffSettingsHelper::normalizeArray($this->alwaysUsedPropertiesSuffixes);
		}

		return $this->normalizedAlwaysUsedPropertiesSuffixes;
	}

	private function getSniffName(string $sniffName): string
	{
		return sprintf('%s.%s', self::NAME, $sniffName);
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $classPointer
	 */
	public function process(\PHP_CodeSniffer\Files\File $phpcsFile, $classPointer): void
	{
		$tokens = $phpcsFile->getTokens();
		$classToken = $tokens[$classPointer];
		$className = ClassHelper::getFullyQualifiedName($phpcsFile, $classPointer);

		/** @var int $openTagPointer */
		$openTagPointer = TokenHelper::findPrevious($phpcsFile, T_OPEN_TAG, $classPointer - 1);
		$useStatements = UseStatementHelper::getUseStatements($phpcsFile, $openTagPointer);

		$reportedProperties = $this->getProperties($phpcsFile, $tokens, $classToken);
		$reportedConstants = $this->getConstants($phpcsFile, $tokens, $classToken);

		$reportedMethods = [];
		$originalMethods = [];
		foreach ($this->getMethods($phpcsFile, $tokens, $classToken) as $methodName => $methodPointer) {
			$normalizedMethodName = $this->getNormalizedMethodName($methodName);
			$reportedMethods[$normalizedMethodName] = $methodPointer;
			$originalMethods[$normalizedMethodName] = $methodName;
		}

		if (count($reportedProperties) + count($reportedMethods) + count($reportedConstants) === 0) {
			return;
		}

		$writeOnlyProperties = [];
		$findUsagesStartTokenPointer = $classToken['scope_opener'] + 1;

		$isCurrentClass = function (int $referencedNamePointer) use ($phpcsFile, $tokens, $useStatements, $className): bool {
			if (in_array($tokens[$referencedNamePointer]['code'], [T_SELF, T_STATIC], true)) {
				return true;
			}

			if ($tokens[$referencedNamePointer]['code'] === T_STRING) {
				$referencedClassName = NamespaceHelper::resolveClassName($phpcsFile, $tokens[$referencedNamePointer]['content'], $useStatements, $referencedNamePointer);
				if ($className === $referencedClassName) {
					return true;
				}
			}

			return false;
		};

		$checkPropertyUsage = function (int $propertyNameTokenPointer, int $thisOrSelfTokenPointer) use ($phpcsFile, $tokens, &$reportedProperties, &$writeOnlyProperties): void {
			$propertyName = $this->getNormalizedPropertyName($tokens[$propertyNameTokenPointer]['content']);

			$possibleNewTokenPointer = TokenHelper::findPreviousEffective($phpcsFile, $thisOrSelfTokenPointer - 1);
			if ($possibleNewTokenPointer !== null && $tokens[$possibleNewTokenPointer]['code'] === T_NEW) {
				unset($reportedProperties[$propertyName]);
				return;
			}

			$possibleAssignTokenPointer = TokenHelper::findNextEffective($phpcsFile, $propertyNameTokenPointer + 1);
			$possibleAssignToken = $tokens[$possibleAssignTokenPointer];
			if (
				in_array($possibleAssignToken['code'], [
					T_EQUAL,
					T_PLUS_EQUAL,
					T_MINUS_EQUAL,
					T_MUL_EQUAL,
					T_DIV_EQUAL,
					T_POW_EQUAL,
					T_MOD_EQUAL,
					T_AND_EQUAL,
					T_OR_EQUAL,
					T_XOR_EQUAL,
					T_SL_EQUAL,
					T_SR_EQUAL,
					T_CONCAT_EQUAL,
				], true)
			) {
				$writeOnlyProperties[$propertyName] = $propertyNameTokenPointer;
				return;
			}

			unset($reportedProperties[$propertyName]);
		};

		$checkObjectOperatorUsage = function (int $objectOperatorTokenPointer, int $thisTokenPointer) use ($phpcsFile, $tokens, $checkPropertyUsage, &$reportedMethods): int {
			$nextTokenPointer = TokenHelper::findNextEffective($phpcsFile, $objectOperatorTokenPointer + 1);
			$nextToken = $tokens[$nextTokenPointer];
			if ($nextToken['code'] !== T_STRING) {
				// $variable-> but not accessing a specific property (e. g. $variable->$foo or $variable->{$foo})
				return $objectOperatorTokenPointer + 1;
			}

			$methodCallTokenPointer = TokenHelper::findNextEffective($phpcsFile, $nextTokenPointer + 1);
			$methodCallToken = $tokens[$methodCallTokenPointer];
			if ($methodCallToken['code'] === T_OPEN_PARENTHESIS) {
				$possibleNewTokenPointer = TokenHelper::findPreviousEffective($phpcsFile, $thisTokenPointer - 1);
				if ($possibleNewTokenPointer === null || $tokens[$possibleNewTokenPointer]['code'] !== T_NEW) {
					// Calling a method on $variable
					unset($reportedMethods[$this->getNormalizedMethodName($nextToken['content'])]);
					return $methodCallTokenPointer + 1;
				}
			}

			$checkPropertyUsage($nextTokenPointer, $thisTokenPointer);

			return $nextTokenPointer + 1;
		};

		$checkDoubleColonUsage = function (int $doubleColonTokenPointer, int $selfTokenPointer) use ($phpcsFile, $tokens, $checkPropertyUsage, &$reportedMethods, &$reportedConstants): int {
			$nextTokenPointer = TokenHelper::findNextEffective($phpcsFile, $doubleColonTokenPointer + 1);
			$nextToken = $tokens[$nextTokenPointer];
			if ($nextToken['code'] !== T_STRING) {
				// self:: or static:: not followed by a string - possible static property access

				if ($nextToken['code'] === T_VARIABLE) {
					$checkPropertyUsage($nextTokenPointer, $selfTokenPointer);
				}
				return $nextTokenPointer + 1;
			}

			$tokenPointerAfterNextToken = TokenHelper::findNextEffective($phpcsFile, $nextTokenPointer + 1);
			$tokenAfterNextToken = $tokens[$tokenPointerAfterNextToken];
			if ($tokenAfterNextToken['code'] !== T_OPEN_PARENTHESIS) {
				// self::string or static::string not followed by ( - possible constant access

				$name = $nextToken['content'];
				unset($reportedConstants[$name]);

				return $tokenPointerAfterNextToken + 1;
			}

			$name = $this->getNormalizedMethodName($nextToken['content']);
			unset($reportedMethods[$name]);

			return $tokenPointerAfterNextToken + 1;
		};

		while (($tokenPointer = TokenHelper::findNext($phpcsFile, [T_NEW, T_HEREDOC, T_DOUBLE_QUOTED_STRING, T_DOUBLE_COLON, T_OBJECT_OPERATOR], $findUsagesStartTokenPointer, $classToken['scope_closer'])) !== null) {
			$token = $tokens[$tokenPointer];

			if (in_array($token['code'], [T_HEREDOC, T_DOUBLE_QUOTED_STRING], true)) {
				if (preg_match_all('~(?<!\\\\)\$this->(.+?\b)(?!\()~', $token['content'], $matches, PREG_PATTERN_ORDER)) {
					foreach ($matches[1] as $propertyInString) {
						if (!isset($reportedProperties[$propertyInString])) {
							continue;
						}

						unset($reportedProperties[$propertyInString]);
					}
				}
				if (preg_match_all('~(?:self|static)::(.+?\b)~', $token['content'], $matches, PREG_PATTERN_ORDER)) {
					foreach ($matches[1] as $constantInString) {
						if (!isset($reportedConstants[$constantInString])) {
							continue;
						}

						unset($reportedConstants[$constantInString]);
					}
				}
				if (preg_match_all('~(?<=\{)\$this->(.+?\b)(?=\()~', $token['content'], $matches, PREG_PATTERN_ORDER)) {
					foreach ($matches[1] as $methodInString) {
						$normalizedMethodInString = $this->getNormalizedMethodName($methodInString);
						if (!isset($reportedMethods[$normalizedMethodInString])) {
							continue;
						}

						unset($reportedMethods[$normalizedMethodInString]);
					}
				}

				$findUsagesStartTokenPointer = $tokenPointer + 1;
			} elseif ($token['code'] === T_OBJECT_OPERATOR) {
				$variableTokenPointer = TokenHelper::findPreviousEffective($phpcsFile, $tokenPointer - 1);
				if ($tokens[$variableTokenPointer]['code'] === T_VARIABLE && $tokens[$variableTokenPointer]['content'] === '$this') {
					$findUsagesStartTokenPointer = $checkObjectOperatorUsage($tokenPointer, $variableTokenPointer);
				} else {
					$possibleThisTokenPointer = $tokenPointer - 1;
					do {
						$possibleThisTokenPointer = TokenHelper::findPreviousLocal($phpcsFile, T_VARIABLE, $possibleThisTokenPointer - 1);
					} while ($possibleThisTokenPointer !== null && $tokens[$possibleThisTokenPointer]['content'] !== '$this');

					if ($possibleThisTokenPointer !== null) {
						$possibleMethodNamePointer = TokenHelper::findNextEffective($phpcsFile, $tokenPointer + 1);
						if ($tokens[$possibleMethodNamePointer]['code'] === T_STRING) {
							$possibleMethodCallPointer = TokenHelper::findNextEffective($phpcsFile, $possibleMethodNamePointer + 1);
							if ($tokens[$possibleMethodCallPointer]['code'] === T_OPEN_PARENTHESIS) {
								$methodName = $this->getNormalizedMethodName($tokens[$possibleMethodNamePointer]['content']);
								if (isset($reportedMethods[$methodName])) {
									unset($reportedMethods[$methodName]);
									$findUsagesStartTokenPointer = $possibleMethodCallPointer + 1;
									continue;
								}
							}
						}
					}

					$findUsagesStartTokenPointer = $tokenPointer + 1;
				}
			} elseif ($token['code'] === T_DOUBLE_COLON) {
				$previousTokenPointer = TokenHelper::findPreviousEffective($phpcsFile, $tokenPointer - 1);
				if ($isCurrentClass($previousTokenPointer)) {
					$findUsagesStartTokenPointer = $checkDoubleColonUsage($tokenPointer, $previousTokenPointer);
				} else {
					$findUsagesStartTokenPointer = $tokenPointer + 1;
				}
			} elseif ($token['code'] === T_NEW) {
				$variableTokenPointer = TokenHelper::findPreviousLocal($phpcsFile, T_VARIABLE, $tokenPointer - 1);
				$nextTokenPointer = TokenHelper::findNextEffective($phpcsFile, $tokenPointer + 1);
				if ($variableTokenPointer !== null && $isCurrentClass($nextTokenPointer)) {
					$scopeMethodPointer = TokenHelper::findPrevious($phpcsFile, T_FUNCTION, $variableTokenPointer - 1);
					for ($i = $tokens[$scopeMethodPointer]['scope_opener']; $i < $tokens[$scopeMethodPointer]['scope_closer']; $i++) {
						if ($tokens[$i]['content'] !== $tokens[$variableTokenPointer]['content']) {
							continue;
						}

						$afterActualTokenPointer = TokenHelper::findNextEffective($phpcsFile, $i + 1);
						if ($tokens[$afterActualTokenPointer]['code'] === T_OBJECT_OPERATOR) {
							$checkObjectOperatorUsage($afterActualTokenPointer, $i);
						} elseif ($tokens[$afterActualTokenPointer]['code'] === T_DOUBLE_COLON) {
							$checkDoubleColonUsage($afterActualTokenPointer, $i);
						}
					}
				}
				$findUsagesStartTokenPointer = $tokenPointer + 1;
			}

			if (count($reportedProperties) + count($reportedMethods) + count($reportedConstants) !== 0) {
				continue;
			}

			return;
		}

		$className = ClassHelper::getName($phpcsFile, $classPointer);

		foreach ($reportedProperties as $name => $propertyTokenPointer) {
			if (isset($writeOnlyProperties[$name])) {
				if (!SuppressHelper::isSniffSuppressed($phpcsFile, $propertyTokenPointer, $this->getSniffName(self::CODE_WRITE_ONLY_PROPERTY))) {
					$phpcsFile->addError(sprintf(
						'Class %s contains write-only property $%s.',
						$className,
						$name
					), $propertyTokenPointer, self::CODE_WRITE_ONLY_PROPERTY);
				}
			} else {
				if (!SuppressHelper::isSniffSuppressed($phpcsFile, $propertyTokenPointer, $this->getSniffName(self::CODE_UNUSED_PROPERTY))) {
					$phpcsFile->addError(sprintf(
						'Class %s contains unused property $%s.',
						$className,
						$name
					), $propertyTokenPointer, self::CODE_UNUSED_PROPERTY);
				}
			}
		}

		foreach ($reportedMethods as $name => $methodTokenPointer) {
			if (SuppressHelper::isSniffSuppressed($phpcsFile, $methodTokenPointer, $this->getSniffName(self::CODE_UNUSED_METHOD))) {
				continue;
			}

			$phpcsFile->addError(sprintf(
				'Class %s contains unused private method %s().',
				$className,
				$originalMethods[$name]
			), $methodTokenPointer, self::CODE_UNUSED_METHOD);
		}

		foreach ($reportedConstants as $name => $constantTokenPointer) {
			if (SuppressHelper::isSniffSuppressed($phpcsFile, $constantTokenPointer, $this->getSniffName(self::CODE_UNUSED_CONSTANT))) {
				continue;
			}

			$phpcsFile->addError(sprintf(
				'Class %s contains unused private constant %s.',
				$className,
				$name
			), $constantTokenPointer, self::CODE_UNUSED_CONSTANT);
		}
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param mixed[] $tokens
	 * @param mixed[] $classToken
	 * @return int[] string(name) => pointer
	 */
	private function getProperties(\PHP_CodeSniffer\Files\File $phpcsFile, array $tokens, array $classToken): array
	{
		$reportedProperties = [];
		$findPropertiesStartTokenPointer = $classToken['scope_opener'] + 1;
		while (true) {
			$propertyTokenPointer = TokenHelper::findNext($phpcsFile, T_VARIABLE, $findPropertiesStartTokenPointer, $classToken['scope_closer']);
			if ($propertyTokenPointer === null) {
				break;
			}

			$visibilityModifierTokenPointer = $this->findVisibilityModifierTokenPointer($phpcsFile, $tokens, $propertyTokenPointer);
			if ($visibilityModifierTokenPointer === null || $tokens[$visibilityModifierTokenPointer]['code'] !== T_PRIVATE) {
				$findPropertiesStartTokenPointer = $propertyTokenPointer + 1;
				continue;
			}

			$findPropertiesStartTokenPointer = $propertyTokenPointer + 1;
			$annotationNames = $this->getAnnotationNames($phpcsFile, $visibilityModifierTokenPointer);

			$alwaysUsedProperty = false;
			foreach ($annotationNames as $annotationName) {
				foreach ($this->getAlwaysUsedPropertiesAnnotations() as $alwaysUsedPropertyAnnotationName) {
					if ($annotationName === $alwaysUsedPropertyAnnotationName) {
						$alwaysUsedProperty = true;
						break 2;
					}

					if (substr($alwaysUsedPropertyAnnotationName, -1) === '\\' && strpos($annotationName, $alwaysUsedPropertyAnnotationName) === 0) {
						$alwaysUsedProperty = true;
						break 2;
					}
				}
			}
			if ($alwaysUsedProperty) {
				continue;
			}

			$propertyToken = $tokens[$propertyTokenPointer];
			$name = substr($propertyToken['content'], 1);

			foreach ($this->getAlwaysUsedPropertiesSuffixes() as $prefix) {
				if (!StringHelper::endsWith($name, $prefix)) {
					continue;
				}

				continue 2;
			}

			$reportedProperties[$name] = $propertyTokenPointer;
		}

		return $reportedProperties;
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $privateTokenPointer
	 * @return string[]
	 */
	private function getAnnotationNames(\PHP_CodeSniffer\Files\File $phpcsFile, int $privateTokenPointer): array
	{
		return array_keys(AnnotationHelper::getAnnotations($phpcsFile, $privateTokenPointer));
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param mixed[] $tokens
	 * @param mixed[] $classToken
	 * @return int[] string(name) => pointer
	 */
	private function getMethods(\PHP_CodeSniffer\Files\File $phpcsFile, array $tokens, array $classToken): array
	{
		$reportedMethods = [];
		$findMethodsStartTokenPointer = $classToken['scope_opener'] + 1;
		while (true) {
			$methodTokenPointer = TokenHelper::findNext($phpcsFile, T_FUNCTION, $findMethodsStartTokenPointer, $classToken['scope_closer']);
			if ($methodTokenPointer === null) {
				break;
			}

			$visibilityModifierTokenPointer = $this->findVisibilityModifierTokenPointer($phpcsFile, $tokens, $methodTokenPointer);
			if ($visibilityModifierTokenPointer === null || $tokens[$visibilityModifierTokenPointer]['code'] !== T_PRIVATE) {
				$findMethodsStartTokenPointer = $methodTokenPointer + 1;
				continue;
			}

			$methodName = FunctionHelper::getName($phpcsFile, $methodTokenPointer);

			if ($methodName !== '__construct') {
				$reportedMethods[$methodName] = $methodTokenPointer;
			}
			$findMethodsStartTokenPointer = $methodTokenPointer + 1;
		}

		return $reportedMethods;
	}

	private function getNormalizedMethodName(string $methodName): string
	{
		return strtolower($methodName);
	}

	private function getNormalizedPropertyName(string $propertyName): string
	{
		return preg_replace('~^\$~', '', $propertyName);
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param mixed[] $tokens
	 * @param mixed[] $classToken
	 * @return int[] string(name) => pointer
	 */
	private function getConstants(\PHP_CodeSniffer\Files\File $phpcsFile, array $tokens, array $classToken): array
	{
		$reportedConstants = [];
		$findConstantsStartTokenPointer = $classToken['scope_opener'] + 1;
		while (true) {
			$constantTokenPointer = TokenHelper::findNext($phpcsFile, T_CONST, $findConstantsStartTokenPointer, $classToken['scope_closer']);
			if ($constantTokenPointer === null) {
				break;
			}

			$visibilityModifierTokenPointer = $this->findVisibilityModifierTokenPointer($phpcsFile, $tokens, $constantTokenPointer);
			if ($visibilityModifierTokenPointer === null || $tokens[$visibilityModifierTokenPointer]['code'] !== T_PRIVATE) {
				$findConstantsStartTokenPointer = $constantTokenPointer + 1;
				continue;
			}

			$namePointer = TokenHelper::findNextEffective($phpcsFile, $constantTokenPointer + 1);
			$constantName = $tokens[$namePointer]['content'];
			$reportedConstants[$constantName] = $constantTokenPointer;

			$findConstantsStartTokenPointer = $constantTokenPointer + 1;
		}

		return $reportedConstants;
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param mixed[] $tokens
	 * @param int $methodTokenPointer
	 * @return int|null
	 */
	private function findVisibilityModifierTokenPointer(\PHP_CodeSniffer\Files\File $phpcsFile, array $tokens, int $methodTokenPointer): ?int
	{
		/** @var int $visibilityModifiedTokenPointer */
		$visibilityModifiedTokenPointer = TokenHelper::findPreviousEffective($phpcsFile, $methodTokenPointer - 1);
		$visibilityModifiedToken = $tokens[$visibilityModifiedTokenPointer];
		if (in_array($visibilityModifiedToken['code'], [T_PUBLIC, T_PROTECTED, T_PRIVATE], true)) {
			return $visibilityModifiedTokenPointer;
		} elseif (in_array($visibilityModifiedToken['code'], [T_ABSTRACT, T_STATIC], true)) {
			return $this->findVisibilityModifierTokenPointer($phpcsFile, $tokens, $visibilityModifiedTokenPointer);
		}

		return null;
	}

}
