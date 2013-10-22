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

class OpenGraphHooks extends Controller {

	public function addOpenGraphDefinition($strContent, $strTemplate) {
		
		if (array_key_exists('opengraph_enable', $GLOBALS['TL_CONFIG'])
				&& $GLOBALS['TL_CONFIG']['opengraph_enable'] === true
				&& $strTemplate == 'fe_page') {

			$strContent = str_replace('<html', '<html prefix="og: http://ogp.me/ns#"', $strContent);
		}
		return $strContent;
	}
	
	public function addOpenGraphTags(PageModel $objPage, LayoutModel $objLayout, PageRegular $objPageRegular) {

		$blnOG = array('image' => false, 'title' => false, 'url'   => false);
		
		if(is_array($GLOBALS['TL_HEAD'])) {
			foreach ($GLOBALS['TL_HEAD'] as $head) {
				$blnOG['image'] = $blnOG['image'] || (strpos($head, 'og:image') > 0);
				$blnOG['title'] = $blnOG['title'] || (strpos($head, 'og:title') > 0);
				$blnOG['url']   = $blnOG['url']   || (strpos($head, 'og:url') > 0);
			}
		}
		
		if (!$blnOG['title']) {
			$GLOBALS['TL_HEAD'][] = OpenGraph::getOgTitleTag($objPage->title);
		}
		
		if (!$blnOG['url']) {
			$url = Environment::get('base').$this->generateFrontendUrl($objPage->row());
			$GLOBALS['TL_HEAD'][] = OpenGraph::getOgUrlTag($url);
		}
		
		if (!$blnOG['image']) {
			
			/* TODO :: pageimage
			$img   = Image::get();
			$GLOBALS['TL_HEAD'][] = OpenGraph::getOgImageTag($img);
			*/
		}
		
		
		/*
		// Get page info
	    $objRootPage = $this->getPageDetails($objPage->rootId);

		if ($objRootPage->addFavicon)
		{
			$objFile = \FilesModel::findByPk($objRootPage->favicon);
	
			if ($objFile !== null && is_file(TL_ROOT . '/' . $objFile->path))
			{
				// make favicon
				$favicon = $this->createIcon($objFile->path, ($objRootPage->rootFavicon ? $objRootPage->alias : ''), $objRootPage->fbFavicon);
			}
		}
		 */
		
		
		/*
		print_r(array(
			$objPage->add_opengraph_image,
			$objPage->opengraph_image
		));
		*/
		
		// TODO $objPage->opengraph_type;

		$GLOBALS['TL_HEAD'][] = OpenGraph::getOgSiteNameTag($objPage->rootTitle);
		
	}
	
}



