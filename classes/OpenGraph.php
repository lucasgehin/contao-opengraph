<?php

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2013 Leo Feyer
 *
 *
 * PHP version 5
 * @copyright  Martin Kozianka 2012-2013 <http://kozianka.de/>
 * @author     Martin Kozianka <http://kozianka.de/>
 * @package    opengraph
 * @license    LGPL
 * @filesource
 */

class OpenGraph {
	public static $TMPL = '<meta property="og:%s" content="%s"/>';

	public static function getOgImageTag($value) {
		return sprintf(self::$TMPL, 'image', $value);
	} 

	public static function getOgTitleTag($value) {
		return sprintf(self::$TMPL, 'title', htmlspecialchars($value));
	}
	
	public static function getOgUrlTag($value) {
		return sprintf(self::$TMPL, 'url', $value);
	}
	
	public static function getOgSiteNameTag($value) {
		return sprintf(self::$TMPL, 'site_name', htmlspecialchars($value));
	}

	public static function getOgTypeTag($value) {
		return sprintf(self::$TMPL, 'type', $value);
	}

	public static function imageSettings() {
		if ($GLOBALS['TL_CONFIG']['opengraph_size']) {
			return unserialize($GLOBALS['TL_CONFIG']['opengraph_size']);
		}
		return array(512, 512, 'center_center');
	}

}



