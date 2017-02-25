<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Files;

use SlevomatCodingStandard\Helpers\StringHelper;

class FilepathNamespaceExtractor
{

	/** @var string[] */
	private $rootNamespaces;

	/** @var bool[] dir(string) => true(bool) */
	private $skipDirs;

	/** @var string[] */
	private $extensions;

	/**
	 * @param string[] $rootNamespaces directory(string) => namespace
	 * @param string[] $skipDirs
	 * @param string[] $extensions index(integer) => extension
	 */
	public function __construct(
		array $rootNamespaces,
		array $skipDirs,
		array $extensions
	)
	{
		$this->rootNamespaces = $rootNamespaces;
		$this->skipDirs = array_fill_keys($skipDirs, true);
		$this->extensions = array_map(function (string $extension): string {
			return strtolower($extension);
		}, $extensions);
	}

	/**
	 * @param string $path
	 * @return string|null
	 */
	public function getTypeNameFromProjectPath(string $path)
	{
		$extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
		if (!in_array($extension, $this->extensions, true)) {
			return null;
		}

		$pathParts = preg_split('~[/\\\]~', $path);
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

		array_unshift($pathParts, $rootNamespace);

		$typeName = implode('\\', array_filter($pathParts, function (string $pathPart): bool {
			return !isset($this->skipDirs[$pathPart]);
		}));

		return substr($typeName, 0, -strlen('.' . $extension));
	}

}
