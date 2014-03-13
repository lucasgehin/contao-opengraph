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

namespace OpenGraph;

class OpenGraphHooks extends \Controller {

	public function addOpenGraphDefinition($strContent, $strTemplate) {


        var_dump($GLOBALS['FE_MOD']['news']);

		if (array_key_exists('opengraph_enable', $GLOBALS['TL_CONFIG'])
				&& $GLOBALS['TL_CONFIG']['opengraph_enable'] === true
                && $strTemplate == 'fe_page'
                && strpos($strContent, 'ogp.me') === false) {
            $strContent = str_replace('<html', '<html prefix="og: http://ogp.me/ns#"', $strContent);
		}
		return $strContent;
	}

	public function addOpenGraphTags(\PageModel $objPage, \LayoutModel $objLayout, \PageRegular $objPageRegular) {

        $objRootPage = \PageModel::findByPk($objPage->rootId);

        if ($objRootPage->opengraph_enable !== '1') {
            // OpenGraph not enabled in root page
            return;
        }

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
			$url = \Environment::get('base').$this->generateFrontendUrl($objPage->row());
			$GLOBALS['TL_HEAD'][] = OpenGraph::getOgUrlTag($url);
		}
		
		if (!$blnOG['image']) {

            $filesModel = null;
            foreach (\PageModel::findParentsById($objPage->id) as $parent) {
                if ($filesModel === null) {
                    $filesModel = \FilesModel::findByUuid($parent->opengraph_image);
                }
            }

            if ($filesModel != null) {
                $imgSize              = deserialize($objRootPage->opengraph_size);
                $img                  = \Image::get($filesModel->path, $imgSize[0], $imgSize[1], $imgSize[2]);
                $GLOBALS['TL_HEAD'][] = OpenGraph::getOgImageTag($img);
            }
		}

		// TODO $objPage->opengraph_type;

		$GLOBALS['TL_HEAD'][] = OpenGraph::getOgSiteNameTag($objPage->rootTitle);

	}


    public function parseArticlesHook($objTemplate, $articleRow, $objModule) {
        global $objPage;

        if ($objModule->opengraph_enable !== '1') {
            return;
        }

        $objRootPage = \PageModel::findByPk($objPage->rootId);

        if ($objRootPage->opengraph_enable !== '1') {
            return;
        }

        if ($GLOBALS['TL_HEAD'] === null) {
            $GLOBALS['TL_HEAD'] = array();
        }

        $GLOBALS['TL_HEAD'][] = OpenGraph::getOgTitleTag($articleRow['headline']);
        $GLOBALS['TL_HEAD'][] = OpenGraph::getOgUrlTag(\Environment::get('base').$objTemplate->link);

        if ($articleRow['addImage'] === '1') {
            $filesModel = \FilesModel::findByUuid($articleRow['singleSRC']);
            if ($filesModel !== null && is_file(TL_ROOT . '/' . $filesModel->path)) {

                $imgSize = deserialize($objRootPage->opengraph_size);
                $ogImage = \Image::get($filesModel->path, $imgSize[0], $imgSize[1], $imgSize[2]);

                $GLOBALS['TL_HEAD'][] = OpenGraph::getOgImageTag(\Environment::get('base').$ogImage);
            }
        }

    }

	
}



