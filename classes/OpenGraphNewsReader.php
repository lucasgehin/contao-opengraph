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

class OpenGraphNewsReader extends ModuleNewsReader {
	
	protected function compile() {
		
		parent::compile();

		if ($this->opengraph_enable !== '1') {
			return;
		}

		$objArticle = \NewsModel::findPublishedByParentAndIdOrAlias(\Input::get('items'), $this->news_archives);
		// $arrArticle = $this->parseArticle($objArticle);
		// var_dump($arrArticle);
		if ($GLOBALS['TL_HEAD'] === null) {
			$GLOBALS['TL_HEAD'] = array();
		}

		$base = Environment::get('base');

		$GLOBALS['TL_HEAD'][] = OpenGraph::getOgTitleTag($objArticle->headline);
		$GLOBALS['TL_HEAD'][] = OpenGraph::getOgUrlTag($base.$this->generateNewsUrl($objArticle));

		if ($objArticle->singleSRC && is_numeric($objArticle->singleSRC)) {
			
			$objModel = \FilesModel::findByPk($objArticle->singleSRC);
			if ($objModel !== null && is_file(TL_ROOT . '/' . $objModel->path)) {
				$imgSet  = OpenGraph::imageSettings();
				$ogImage = $base.Image::get($objModel->path, $imgSet[0], $imgSet[1], $imgSet[2]);
				
				$GLOBALS['TL_HEAD'][] = OpenGraph::getOgImageTag($ogImage);
			}
		}
	}

}