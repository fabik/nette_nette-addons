<?php

namespace NetteAddons\Model\Importers\GitHub;

use Nette\Utils\Strings;
use Nette\Http\Url;



/**
 * GitHub repository API implementation.
 *
 * This class is not aware how it will be used.
 *
 * @link http://developer.github.com/v3/ GitHub API documentation
 * @author Patrik Votoček
 * @author Jan Tvrdík
 */
class Repository extends \Nette\Object
{
	/** @var \NetteAddons\CurlRequestFactory */
	private $curl;

	/** @var string */
	private $vendor;

	/** @var string */
	private $name;

	/** @var string */
	public $baseUrl = 'https://api.github.com';

	/** @var string */
	private $apiVersion;



	/**
	 * @param \NetteAddons\Curl
	 * @param string
	 * @param string
	 */
	public function __construct($apiVersion, \NetteAddons\CurlRequestFactory $curl, $vendor, $name)
	{
		$this->apiVersion;
		$this->curl = $curl;
		$this->vendor = $vendor;
		$this->name = $name;
	}



	/**
	 * Calls remote API.
	 *
	 * @param  string
	 * @return mixed json-decoded result
	 * @throws \NetteAddons\IOException
	 */
	protected function exec($path)
	{
		try {
			$request = $this->curl->create(new Url($this->baseUrl . '/' . ltrim($path, '/')));
			$request->setOption(CURLOPT_HTTPHEADER, array(
				"Accept: application/vnd.github.{$this->apiVersion}+json",
			));

			return \Nette\Utils\Json::decode($request->execute());

		} catch (\NetteAddons\CurlException $e) {
			throw new \NetteAddons\IOException('cURL execution failed.', NULL, $e);

		} catch (\Nette\Utils\JsonException $e) {
			throw new \NetteAddons\IOException('GitHub API returned invalid JSON.', NULL, $e);
		}
	}



	/**
	 * @return string
	 */
	public function getVendor()
	{
		return $this->vendor;
	}



	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}



	/**
	 * Returns URL to this repository.
	 *
	 * @todo   Use GitHub API?
	 * @return string
	 */
	public function getUrl()
	{
		return "https://github.com/{$this->vendor}/{$this->name}";
	}



	/**
	 * Returns repository metadata.
	 *
	 * @link http://developer.github.com/v3/repos/#get
	 * @return \stdClass
	 * @throws \NetteAddons\IOException
	 */
	public function getMetadata()
	{
		return $this->exec("/repos/{$this->vendor}/{$this->name}");
	}



	/**
	 * Returns Git "tree" specified by hash.
	 *
	 * @link http://developer.github.com/v3/git/trees/#get-a-tree
	 * @param  string commit or tree hash, branch or tag
	 * @return \stdClass
	 * @throws \NetteAddons\IOException
	 */
	public function getTree($hash)
	{
		return $this->exec("/repos/{$this->vendor}/{$this->name}/git/trees/$hash");
	}



	/**
	 * Gets file content.
	 *
	 * @link http://developer.github.com/v3/repos/contents/#get-contents
	 * @param  string commit hash, branch or tag
	 * @param  string
	 * @return string
	 * @throws \NetteAddons\IOException
	 */
	public function getFileContent($hash, $path)
	{
		$data = $this->exec("/repos/{$this->vendor}/{$this->name}/contents/$path?ref=$hash");
		return $this->processContentResponse($data)->content;
	}



	/**
	 * Returns readme content or NULL if readme does not exist.
	 *
	 * @link http://developer.github.com/v3/repos/contents/#get-the-readme
	 * @param  string commit hash, branch or tag
	 * @return string|NULL
	 * @throws \NetteAddons\IOException
	 */
	public function getReadme($hash)
	{
		try {
			$data = $this->exec("/repos/{$this->vendor}/{$this->name}/readme?ref=$hash");

		} catch (\NetteAddons\HttpException $e) {
			if ($e->getCode() === 404) {
				return NULL;
			}
			throw $e;
		}

		return $this->processContentResponse($data);
	}



	/**
	 * @param string
	 * @return \stdClass
	 * @throws \NetteAddons\IOException
	 */
	protected function processContentResponse($data)
	{
		if (!$data instanceof \stdClass || !isset($data->encoding, $data->content)) {
			throw new \NetteAddons\IOException('GitHub API returned unexpected response.');
		}

		if ($data->encoding === 'base64') {
			$data->content = base64_decode($data->content);

		} elseif ($data->encoding !== 'utf-8') {
			throw new \NetteAddons\IOException('GitHub API returned file content in unknown encoding.');
		}

		return $data;
	}



	/**
	 * Returns list of repository tags.
	 *
	 * @link http://developer.github.com/v3/repos/#list-tags
	 * @return array (tagName => commitHash)
	 * @throws \NetteAddons\IOException
	 */
	public function getTags()
	{
		$data = $this->exec("/repos/{$this->vendor}/{$this->name}/tags");
		if (!is_array($data)) {
			throw new \NetteAddons\IOException('GitHub API returned unexpected value.');
		}

		$tags = array();
		foreach ($data as $tag) {
			$tags[$tag->name] = $tag->commit->sha;
		}
		return $tags;
	}



	/**
	 * Returns list of repository branches.
	 *
	 * @link http://developer.github.com/v3/repos/#list-branches
	 * @return array (branchName => commitHash)
	 * @throws \NetteAddons\IOException
	 */
	public function getBranches()
	{
		$data = $this->exec("/repos/{$this->vendor}/{$this->name}/branches");
		if (!is_array($data)) {
			throw new \NetteAddons\IOException('GitHub API returned unexpected value.');
		}

		$branches = array();
		foreach ($data as $branch) {
			$branches[$branch->name] = $branch->commit->sha;
		}
		return $branches;
	}



	/**
	 * Returns download link.
	 *
	 * @todo Implement it using GitHub API?
	 * @param  string
	 * @param  string
	 * @return string
	 * @throws \NetteAddons\NotSupportedException if $type is other than 'zip'
	 */
	public function getArchiveLink($type, $hash)
	{
		if ($type === 'zip') {
			return "https://github.com/{$this->vendor}/{$this->name}/zipball/$hash";

		} else {
			throw new \NetteAddons\NotSupportedException();
		}
	}
}
