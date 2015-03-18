<?php

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2015 Leo Feyer
 *
 *
 * PHP version 5
 * @copyright  Martin Kozianka 2012-2015 <http://kozianka.de/>
 * @author     Martin Kozianka <http://kozianka.de/>
 * @package    opengraph
 * @license    LGPL
 * @filesource
 */

namespace ContaoOpenGraph;

use Contao\Image;

class OpenGraphHooks extends \Controller {

    // https://developers.facebook.com/docs/sharing/best-practices#images
    public static $arrResizeMode = [
        'mode'   => 'crop',
        'width'  => 1200,
        'height' => 630,
        'ratio'  => 1.91
    ];

	public function addOpenGraphDefinition($strContent, $strTemplate) {

        global $objPage;
        $objRootPage = \PageModel::findByPk($objPage->rootId);
        if ($objRootPage->opengraph_enable !== '1') {
            // OpenGraph not enabled in root page
            return $strContent;
        }

        if ($strTemplate == 'fe_page' && strpos($strContent, 'ogp.me') === false) {
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

        $blnOG = array(
            'site_name'   => false,
            'title'       => false,
            'description' => false,
            'url'         => false,
            'image'       => false
        );

		if(is_array($GLOBALS['TL_HEAD'])) {
			foreach ($GLOBALS['TL_HEAD'] as $head) {
                $blnOG['site_name']   = $blnOG['site_name'] || (strpos($head, 'og:site_name') > 0);
                $blnOG['title']       = $blnOG['title'] || (strpos($head, 'og:title') > 0);
                $blnOG['description'] = $blnOG['description'] || (strpos($head, 'og:description') > 0);
                $blnOG['url']         = $blnOG['url'] || (strpos($head, 'og:url') > 0);
				$blnOG['image']       = $blnOG['image'] || (strpos($head, 'og:image') > 0);
			}
		}

        if (!$blnOG['site_name']) {
            $GLOBALS['TL_HEAD'][] = OpenGraph::getOgSiteNameTag($objPage->rootPageTitle);
        }

        if (!$blnOG['title']) {
			$GLOBALS['TL_HEAD'][] = OpenGraph::getOgTitleTag($objPage->title);
        }

        if (!$blnOG['description'] && $objPage->description) {
            $GLOBALS['TL_HEAD'][] = OpenGraph::getOgDescriptionTag($objPage->description);
        }

		if (!$blnOG['url']) {
			$url = \Environment::get('base').$this->generateFrontendUrl($objPage->row());
			$GLOBALS['TL_HEAD'][] = OpenGraph::getOgUrlTag($url);
		}

        // TODO $objPage->opengraph_type;

		if (!$blnOG['image']) {

            // TODO Enable or disable recursive search

            $filesModel = null;
            foreach (\PageModel::findParentsById($objPage->id) as $parent) {
                if ($filesModel === null) {
                    $filesModel = \FilesModel::findByUuid($parent->opengraph_image);
                }
            }

            if ($filesModel !== null && is_file(TL_ROOT . '/' . $filesModel->path)) {
                $GLOBALS['TL_HEAD'][] = OpenGraph::getOgImageTag(self::getImageUrl($filesModel));
            }
		}

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

        // TODO $GLOBALS['TL_HEAD'][] = OpenGraph::getOgDescriptionTag($objPage->description);

        if ($articleRow['addImage'] === '1') {
            $filesModel = \FilesModel::findByUuid($articleRow['singleSRC']);
            if ($filesModel !== null && is_file(TL_ROOT . '/' . $filesModel->path)) {
                $GLOBALS['TL_HEAD'][] = OpenGraph::getOgImageTag(self::getImageUrl($filesModel));
            }
        }
    }

    /**
     * Get url for given file model
     *
     * @param \FilesModel $filesModel
     *
     * @return string Absolute url for given file model
     */
    public static function getImageUrl($filesModel) {
        $arrResize = self::arrResizeMode;
        $fileObj   = new \File($filesModel->path);

        // Do not enlarge the image
        if ($fileObj->width < $arrResize['width']) {
            $arrResize['height'] = floor($fileObj->width / $arrResize['ratio']);
            $arrResize['width']  = $fileObj->width;
        }

        $ogImage = \Image::get($filesModel->path, $arrResize['width'], $arrResize['height'], $arrResize['mode']);
        return \Environment::get('base').$ogImage;
    }
}
