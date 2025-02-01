<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Namespaces;

use SlevomatCodingStandard\Helpers\FunctionHelper;
use SlevomatCodingStandard\Helpers\ReferencedName;
use function array_merge;

class FullyQualifiedGlobalFunctionsSniff
	extends AbstractFullyQualifiedGlobalReference
{

	public bool $includeSpecialFunctions = false;

	/**
	 * @return list<string>
	 */
	protected function getNormalizedInclude(): array
	{
		$include = parent::getNormalizedInclude();

		if ($this->includeSpecialFunctions) {
			$include = array_merge($include, FunctionHelper::SPECIAL_FUNCTIONS);
		}

		return $include;
	}

	protected function getNotFullyQualifiedMessage(): string
	{
		return 'Function %s() should be referenced via a fully qualified name.';
	}

	protected function isCaseSensitive(): bool
	{
		return false;
	}

	protected function isValidType(ReferencedName $name): bool
	{
		return $name->isFunction();
	}

}
