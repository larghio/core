<?php
/**
 * Copyright (c) 2013 Bart Visscher <bartv@thisnet.nl>
 * This file is licensed under the Affero General Public License version 3 or
 * later.
 * See the COPYING-README file.
 */

namespace OC\Template;

abstract class ResourceLocator {
	protected $theme;
	protected $form_factor;

	protected $mapping;
	protected $serverroot;
	protected $thirdpartyroot;
	protected $webroot;

	protected $resources = array();

	public function __construct( $theme, $form_factor, $core_map, $party_map ) {
		$this->theme = $theme;
		$this->form_factor = $form_factor;
		$this->mapping = $core_map + $party_map;
		$this->serverroot = key($core_map);
		$this->thirdpartyroot = key($party_map);
		$this->webroot = $this->mapping[$this->serverroot];
	}

	abstract public function doFind( $resource );
	abstract public function doFindTheme( $resource );

	public function find( $resources ) {
		try {
			foreach($resources as $resource) {
				$this->doFind($resource);
			}
			if (!empty($this->theme)) {
				foreach($resources as $resource) {
					$this->doFindTheme($resource);
				}
			}
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage().' formfactor:'.$this->form_factor
						.' serverroot:'.$this->serverroot);
		}
	}

	/*
	 * @brief append the $file resource if exist at $root
	 * @param $root path to check
	 * @param $file the filename
	 * @param $web base for path, default map $root to $webroot
	 */
	protected function appendIfExist($root, $file, $webroot = null) {
		if (is_file($root.'/'.$file)) {
			return $this->append($root, $file, $webroot);
		}
		return false;
	}

	/*
	 * Append the $file resource at $root
	 * @param $root path to check
	 * @param $file the filename
	 * @param $web base for path, default map $root to $webroot
	 * @return boolean Always true
	 */
	protected function append($root, $file, $webroot = null) {
		if (!$webroot) {
			$webroot = $this->mapping[$root];
		}
		$this->resources[] = array($root, $webroot, $file);
		return true;
	}

	public function getResources() {
		return $this->resources;
	}
}
