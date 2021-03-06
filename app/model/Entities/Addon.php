<?php

namespace NetteAddons\Model;

use Nette;
use Nette\Utils\Strings;
use Nette\DateTime;



/**
 * @author Filip Procházka <filip.prochazka@kdyby.org>
 */
class Addon extends Nette\Object
{
	/** @var int */
	public $id;

	/** @var string */
	public $name;

	/** @var string */
	public $composerName;

	/** @var int */
	public $userId;

	/** @var string single line description */
	public $shortDescription;

	/** @var string */
	public $description;

	/** @var string */
	public $descriptionFormat = 'texy';

	/** @var string default license for new versions */
	public $defaultLicense;

	/** @var string|NULL repository URL	 */
	public $repository;

	/** @var string|NULL */
	public $repositoryHosting;

	/** @var string|NULL URL to addon demo. */
	public $demo;

	/** @var DateTime */
	public $updatedAt;

	/** @var AddonVersion[] (versionNumber => AddonVersion) */
	public $versions = array();

	/** @var Tag[]|string[]|int[] (tagId => Tag (from db) or # => tagName (new user-created tags) or # => tagId */
	public $tags = array();

	/** @var int total times this addon was downloaded */
	public $totalDownloadsCount = 0;

	/** @var int total times this addon was installed using composer */
	public $totalInstallsCount = 0;


	/**
	 * Creates Addon entity from Nette\Database row.
	 *
	 * @todo   Consider lazy loading for versions and tags.
	 *
	 * @param  Nette\Database\Table\ActiveRow
	 * @return Addon
	 */
	public static function fromActiveRow(Nette\Database\Table\ActiveRow $row)
	{
		$addon = new static;
		$addon->id = (int) $row->id;
		$addon->name = $row->name;
		$addon->composerName = $row->composerName;
		$addon->userId = (int) $row->user->id;
		$addon->shortDescription = $row->shortDescription;
		$addon->description = $row->description;
		$addon->descriptionFormat = $row->descriptionFormat;
		$addon->defaultLicense = $row->defaultLicense;
		$addon->repository = $row->repository;
		$addon->repositoryHosting = $row->repositoryHosting;
		$addon->demo = $row->demo;
		$addon->updatedAt = ($row->updatedAt ? DateTime::from($row->updatedAt) : NULL);
		$addon->totalDownloadsCount = $row->totalDownloadsCount ?: 0;
		$addon->totalInstallsCount = $row->totalInstallsCount ?: 0;

		foreach ($row->related('versions') as $versionRow) {
			$version = AddonVersion::fromActiveRow($versionRow);
			$version->addon = $addon;
			$addon->versions[$version->version] = $version;
		}

		foreach ($row->related('tags') as $tagRow) {
			$addon->tags[$tagRow->tag->id] = Tag::fromActiveRow($tagRow->tag);
		}

		return $addon;
	}



	/**
	 * @return int[]
	 */
	public function getTagsIds()
	{
		$ids = array();
		foreach ($this->tags as $tag) {
			if ($tag instanceof Tag) {
				$ids[] = $tag->id;
			} elseif (is_int($tag) || ctype_digit($tag)) {
				$ids[] = (int) $tag;
			}
		}
		return $ids;

		/*if (empty($this->tags)) {
			return array();
		} else if (count(array_filter($this->tags, 'ctype_digit')) === count($this->tags)) {
			return array_map('intval', $this->tags); // TODO: remove converting to int
		} else {
			return array_keys($this->tags);
		}*/
	}
}
