<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Sniffs\Files;

class FilepathNamespaceExtractorStub extends FilepathNamespaceExtractor
{

	/** @var string */
	private $currentDir;

	/**
	 * @param string[] $rootNamespaces directory(string) => namespace
	 * @param string[] $skipDirs
	 * @param string[] $extensions index(integer) => extension
	 * @param string $currentDir
	 */
	public function __construct(array $rootNamespaces, array $skipDirs, array $extensions, string $currentDir)
	{
		parent::__construct($rootNamespaces, $skipDirs, $extensions);

		$this->currentDir = $currentDir;
	}

	protected function getCurrentDir(): string
	{
		return $this->currentDir;
	}

}
