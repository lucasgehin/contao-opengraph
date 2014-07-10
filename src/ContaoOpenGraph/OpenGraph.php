<?php

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2014 Leo Feyer
 *
 *
 * PHP version 5
 * @copyright  Martin Kozianka 2012-2014 <http://kozianka.de/>
 * @author     Martin Kozianka <http://kozianka.de/>
 * @package    opengraph
 * @license    LGPL
 * @filesource
 */
namespace ContaoOpenGraph;

class OpenGraph {

	public static function getOgImageTag($value) {
        return '<meta property="og:image" content="'.$value.'"/>';
	}

	public static function getOgTitleTag($value) {
        return '<meta property="og:title" content="'.htmlspecialchars($value).'"/>';
	}
	
	public static function getOgUrlTag($value) {
        return '<meta property="og:url" content="'.$value.'"/>';
	}
	
	public static function getOgSiteNameTag($value) {
        return '<meta property="og:site_name" content="'.htmlspecialchars($value).'"/>';
	}

	public static function getOgTypeTag($value) {
        return '<meta property="og:type" content="'.$value.'"/>';
	}

}



