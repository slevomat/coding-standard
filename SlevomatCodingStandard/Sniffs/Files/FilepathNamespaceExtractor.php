<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Files;

use SlevomatCodingStandard\Helpers\StringHelper;

class FilepathNamespaceExtractor
{

	/** @var string[] */
	private $rootNamespaces;

	/** @var boolean[] dir(string) => true(boolean) */
	private $skipDirs;

	/**
	 * @param string[] $rootNamespaces directory(string) => namespace
	 * @param string[] $skipDirs
	 */
	public function __construct(
		array $rootNamespaces,
		array $skipDirs
	)
	{
		$this->rootNamespaces = $rootNamespaces;
		$this->skipDirs = array_fill_keys($skipDirs, true);
	}

	/**
	 * @param string $path
	 * @return string|null
	 */
	public function getTypeNameFromProjectPath($path)
	{
		if (pathinfo($path, PATHINFO_EXTENSION) !== 'php') {
			return null;
		}

		$pathParts = explode(DIRECTORY_SEPARATOR, $path);
		$rootNamespace = null;
		while (count($pathParts) > 0) {
			array_shift($pathParts);
			foreach ($this->rootNamespaces as $directory => $namespace) {
				if (StringHelper::startsWith(implode('/', $pathParts) . '/', $directory . '/')) {
					for ($i = 0; $i < count(explode('/', $directory)); $i++) {
						array_shift($pathParts);
					}

					$rootNamespace = $namespace;
					break 2;
				}
			}
		}

		if ($rootNamespace === null) {
			return null;
		}

		if (count($pathParts) === 0) {
			return null;
		}

		array_unshift($pathParts, $rootNamespace);

		$typeName = implode('\\', array_filter($pathParts, function ($pathPart) {
			return !isset($this->skipDirs[$pathPart]);
		}));

		return substr($typeName, 0, -strlen('.php'));
	}

}
