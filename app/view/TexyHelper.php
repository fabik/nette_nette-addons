<?php

namespace NetteAddons;

use Texy;
use TexyConfigurator;



/**
 * @author Jan Marek
 */
class TexyHelper
{
	/** @var Texy */
	private $texy = NULL;



	public function __invoke($text)
	{
		if ($this->texy === NULL) {
			$this->texy = $this->createTexy();
		}

		return $this->texy->process($text);
	}



	private function createTexy()
	{
		$texy = new Texy();
		TexyConfigurator::safeMode($texy);
		return $texy;
	}
}
