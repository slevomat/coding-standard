<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

use SlevomatCodingStandard\Helpers\NamespaceHelper;
use SlevomatCodingStandard\Helpers\SniffSettingsHelper;
use SlevomatCodingStandard\Helpers\UseStatementHelper;

class UseOnlyWhitelistedNamespacesSniff implements \PHP_CodeSniffer\Sniffs\Sniff
{

	const CODE_NON_FULLY_QUALIFIED = 'NonFullyQualified';

	/** @var bool */
	public $allowUseFromRootNamespace = false;

	/** @var string[] */
	public $namespacesRequiredToUse = [];

	/** @var string[]|null */
	private $normalizedNamespacesRequiredToUse;

	/**
	 * @return int[]
	 */
	public function register(): array
	{
		return [
			T_USE,
		];
	}

	/**
	 * @return string[]
	 */
	private function getNamespacesRequiredToUse(): array
	{
		if ($this->namespacesRequiredToUse !== null && $this->normalizedNamespacesRequiredToUse === null) {
			$this->normalizedNamespacesRequiredToUse = SniffSettingsHelper::normalizeArray($this->namespacesRequiredToUse);
		}

		return $this->normalizedNamespacesRequiredToUse;
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $usePointer
	 */
	public function process(\PHP_CodeSniffer\Files\File $phpcsFile, $usePointer)
	{
		if (
			UseStatementHelper::isAnonymousFunctionUse($phpcsFile, $usePointer)
			|| UseStatementHelper::isTraitUse($phpcsFile, $usePointer)
		) {
			return;
		}

		$className = UseStatementHelper::getFullyQualifiedTypeNameFromUse($phpcsFile, $usePointer);
		if ($this->allowUseFromRootNamespace && !NamespaceHelper::isQualifiedName($className)) {
			return;
		}

		foreach ($this->getNamespacesRequiredToUse() as $namespace) {
			if (NamespaceHelper::isTypeInNamespace($className, $namespace)) {
				return;
			}
		}

		$phpcsFile->addError(sprintf(
			'Type %s should not be used, but referenced via a fully qualified name.',
			$className
		), $usePointer, self::CODE_NON_FULLY_QUALIFIED);
	}

}
