<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Classes;

use SlevomatCodingStandard\Helpers\AnnotationHelper;
use SlevomatCodingStandard\Helpers\ClassHelper;
use SlevomatCodingStandard\Helpers\FunctionHelper;
use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use SlevomatCodingStandard\Helpers\StringHelper;
use SlevomatCodingStandard\Helpers\SuppressHelper;
use SlevomatCodingStandard\Helpers\TokenHelper;

class UnusedPrivateElementsSniff implements \PHP_CodeSniffer\Sniffs\Sniff
{

	const NAME = 'SlevomatCodingStandard.Classes.UnusedPrivateElements';

	const CODE_UNUSED_PROPERTY = 'UnusedProperty';

	const CODE_WRITE_ONLY_PROPERTY = 'WriteOnlyProperty';

	const CODE_UNUSED_METHOD = 'UnusedMethod';

	const CODE_UNUSED_CONSTANT = 'UnusedConstant';

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
	public function process(\PHP_CodeSniffer\Files\File $phpcsFile, $classPointer)
	{
		$tokens = $phpcsFile->getTokens();
		$classToken = $tokens[$classPointer];
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

		$checkObjectOperatorUsage = function (int $objectOperatorTokenPointer) use ($phpcsFile, $tokens, &$reportedMethods, &$reportedProperties, &$writeOnlyProperties) {
			$propertyNameTokenPointer = TokenHelper::findNextEffective($phpcsFile, $objectOperatorTokenPointer + 1);
			$propertyNameToken = $tokens[$propertyNameTokenPointer];
			$name = $propertyNameToken['content'];
			if ($propertyNameToken['code'] !== T_STRING) {
				// $variable-> but not accessing a specific property (e. g. $variable->$foo or $variable->{$foo})
				return $objectOperatorTokenPointer + 1;
			}
			$methodCallTokenPointer = TokenHelper::findNextEffective($phpcsFile, $propertyNameTokenPointer + 1);
			$methodCallToken = $tokens[$methodCallTokenPointer];
			if ($methodCallToken['code'] === T_OPEN_PARENTHESIS) {
				// calling a method on $variable
				unset($reportedMethods[$this->getNormalizedMethodName($name)]);
				return $methodCallTokenPointer + 1;
			}

			$assignTokenPointer = TokenHelper::findNextEffective($phpcsFile, $propertyNameTokenPointer + 1);
			$assignToken = $tokens[$assignTokenPointer];
			if ($assignToken['code'] === T_EQUAL) {
				// assigning value to a property - note possible write-only property
				$writeOnlyProperties[$name] = $propertyNameTokenPointer;
				return $assignTokenPointer + 1;
			}

			if (isset($reportedProperties[$name])) {
				unset($reportedProperties[$name]);
			}

			return $propertyNameTokenPointer + 1;
		};

		$checkDoubleColonUsage = function (int $doubleColonTokenPointer) use ($phpcsFile, $tokens, &$reportedMethods, &$reportedConstants): int {
			$methodNameTokenPointer = TokenHelper::findNextEffective($phpcsFile, $doubleColonTokenPointer + 1);
			$methodNameToken = $tokens[$methodNameTokenPointer];
			if ($methodNameToken['code'] !== T_STRING) {
				// self:: or static:: not followed by a string - possible static property access
				return $methodNameTokenPointer + 1;
			}

			$methodCallTokenPointer = TokenHelper::findNextEffective($phpcsFile, $methodNameTokenPointer + 1);
			$methodCallToken = $tokens[$methodCallTokenPointer];
			if ($methodCallToken['code'] !== T_OPEN_PARENTHESIS) {
				$name = $methodNameToken['content'];
				if (isset($reportedConstants[$name])) {
					unset($reportedConstants[$name]);
				}

				// self::string or static::string not followed by ( - possible constant access
				return $methodCallTokenPointer + 1;
			}

			$name = $this->getNormalizedMethodName($methodNameToken['content']);
			if (isset($reportedMethods[$name])) {
				unset($reportedMethods[$name]);
			}

			return $methodCallTokenPointer + 1;
		};

		while (($tokenPointer = TokenHelper::findNext($phpcsFile, [T_NEW, T_DOUBLE_QUOTED_STRING, T_DOUBLE_COLON, T_OBJECT_OPERATOR], $findUsagesStartTokenPointer, $classToken['scope_closer'])) !== null) {
			$token = $tokens[$tokenPointer];
			if ($token['code'] === T_DOUBLE_QUOTED_STRING) {
				if (preg_match_all('~(?<!\\\\)\$this->(.+?\b)(?!\()~', $token['content'], $matches, PREG_PATTERN_ORDER)) {
					foreach ($matches[1] as $propertyInString) {
						if (isset($reportedProperties[$propertyInString])) {
							unset($reportedProperties[$propertyInString]);
						}
					}
				}
				if (preg_match_all('~(?<=\{)\$this->(.+?\b)(?=\()~', $token['content'], $matches, PREG_PATTERN_ORDER)) {
					foreach ($matches[1] as $methodInString) {
						$normalizedMethodInString = $this->getNormalizedMethodName($methodInString);
						if (isset($reportedMethods[$normalizedMethodInString])) {
							unset($reportedMethods[$normalizedMethodInString]);
						}
					}
				}

				$findUsagesStartTokenPointer = $tokenPointer + 1;
			} elseif ($token['code'] === T_OBJECT_OPERATOR) {
				$variableTokenPointer = TokenHelper::findPreviousEffective($phpcsFile, $tokenPointer - 1);
				if ($tokens[$variableTokenPointer]['code'] === T_VARIABLE && $tokens[$variableTokenPointer]['content'] === '$this') {
					$findUsagesStartTokenPointer = $checkObjectOperatorUsage($tokenPointer);
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
				if (in_array($tokens[$previousTokenPointer]['code'], [T_SELF, T_STATIC], true)) {
					$findUsagesStartTokenPointer = $checkDoubleColonUsage($tokenPointer);
				} else {
					$findUsagesStartTokenPointer = $tokenPointer + 1;
				}
			} elseif ($token['code'] === T_NEW) {
				$variableTokenPointer = TokenHelper::findPreviousLocal($phpcsFile, T_VARIABLE, $tokenPointer - 1);
				$nextTokenPointer = TokenHelper::findNextEffective($phpcsFile, $tokenPointer + 1);
				if ($variableTokenPointer !== null && in_array($tokens[$nextTokenPointer]['code'], [T_SELF, T_STATIC], true)) {
					$scopeMethodPointer = TokenHelper::findPrevious($phpcsFile, T_FUNCTION, $variableTokenPointer - 1);
					for ($i = $tokens[$scopeMethodPointer]['scope_opener']; $i < $tokens[$scopeMethodPointer]['scope_closer']; $i++) {
						if ($tokens[$i]['content'] === $tokens[$variableTokenPointer]['content']) {
							$afterActualTokenPointer = TokenHelper::findNextEffective($phpcsFile, $i + 1);
							if ($tokens[$afterActualTokenPointer]['code'] === T_OBJECT_OPERATOR) {
								$checkObjectOperatorUsage($afterActualTokenPointer);
							} elseif ($tokens[$afterActualTokenPointer]['code'] === T_DOUBLE_COLON) {
								$checkDoubleColonUsage($afterActualTokenPointer);
							}
						}
					}
				}
				$findUsagesStartTokenPointer = $tokenPointer + 1;
			}

			if (count($reportedProperties) + count($reportedMethods) + count($reportedConstants) === 0) {
				return;
			}
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
			if (!SuppressHelper::isSniffSuppressed($phpcsFile, $methodTokenPointer, $this->getSniffName(self::CODE_UNUSED_METHOD))) {
				$phpcsFile->addError(sprintf(
					'Class %s contains unused private method %s().',
					$className,
					$originalMethods[$name]
				), $methodTokenPointer, self::CODE_UNUSED_METHOD);
			}
		}

		foreach ($reportedConstants as $name => $constantTokenPointer) {
			if (!SuppressHelper::isSniffSuppressed($phpcsFile, $constantTokenPointer, $this->getSniffName(self::CODE_UNUSED_CONSTANT))) {
				$phpcsFile->addError(sprintf(
					'Class %s contains unused private constant %s.',
					$className,
					$name
				), $constantTokenPointer, self::CODE_UNUSED_CONSTANT);
			}
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
		while (($propertyTokenPointer = TokenHelper::findNext($phpcsFile, T_VARIABLE, $findPropertiesStartTokenPointer, $classToken['scope_closer'])) !== null) {
			$visibilityModifiedTokenPointer = TokenHelper::findPreviousEffective($phpcsFile, $propertyTokenPointer - 1);
			$visibilityModifiedToken = $tokens[$visibilityModifiedTokenPointer];
			if ($visibilityModifiedToken['code'] !== T_PRIVATE) {
				$findPropertiesStartTokenPointer = $propertyTokenPointer + 1;
				continue;
			}

			$findPropertiesStartTokenPointer = $propertyTokenPointer + 1;
			$phpDocTags = $this->getPhpDocTags($phpcsFile, $visibilityModifiedTokenPointer);
			foreach ($phpDocTags as $tag) {
				preg_match('#([@a-zA-Z\\\]+)#', $tag, $matches);
				if (in_array($matches[1], $this->getAlwaysUsedPropertiesAnnotations(), true)) {
					continue 2;
				}
			}

			$propertyToken = $tokens[$propertyTokenPointer];
			$name = substr($propertyToken['content'], 1);

			foreach ($this->getAlwaysUsedPropertiesSuffixes() as $prefix) {
				if (StringHelper::endsWith($name, $prefix)) {
					continue 2;
				}
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
	private function getPhpDocTags(\PHP_CodeSniffer\Files\File $phpcsFile, int $privateTokenPointer): array
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
		while (($methodTokenPointer = TokenHelper::findNext($phpcsFile, T_FUNCTION, $findMethodsStartTokenPointer, $classToken['scope_closer'])) !== null) {
			$visibilityModifier = $this->findVisibilityModifier($phpcsFile, $tokens, $methodTokenPointer);
			if ($visibilityModifier === null || $visibilityModifier !== T_PRIVATE) {
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
		while (($constantTokenPointer = TokenHelper::findNext($phpcsFile, T_CONST, $findConstantsStartTokenPointer, $classToken['scope_closer'])) !== null) {
			$visibilityModifier = $this->findVisibilityModifier($phpcsFile, $tokens, $constantTokenPointer);
			if ($visibilityModifier === null || $visibilityModifier !== T_PRIVATE) {
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
	private function findVisibilityModifier(\PHP_CodeSniffer\Files\File $phpcsFile, array $tokens, int $methodTokenPointer)
	{
		$visibilityModifiedTokenPointer = TokenHelper::findPreviousEffective($phpcsFile, $methodTokenPointer - 1);
		$visibilityModifiedToken = $tokens[$visibilityModifiedTokenPointer];
		if (in_array($visibilityModifiedToken['code'], [T_PUBLIC, T_PROTECTED, T_PRIVATE], true)) {
			return $visibilityModifiedToken['code'];
		} elseif (in_array($visibilityModifiedToken['code'], [T_ABSTRACT, T_STATIC], true)) {
			return $this->findVisibilityModifier($phpcsFile, $tokens, $visibilityModifiedTokenPointer);
		}

		return null;
	}

}
