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

class OpenGraphHooks extends \Controller {

	public function addOpenGraphDefinition($strContent, $strTemplate) {

        global $objPage;

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
        if (!$blnOG['description']) {
            $GLOBALS['TL_HEAD'][] = OpenGraph::getOgDescriptionTag($objPage->description);
        }

		if (!$blnOG['url']) {
			$url = \Environment::get('base').$this->generateFrontendUrl($objPage->row());
			$GLOBALS['TL_HEAD'][] = OpenGraph::getOgUrlTag($url);
		}

        $GLOBALS['TL_HEAD'][] = OpenGraph::getOgSiteNameTag($objPage->rootPageTitle);

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

            if($objRootPage->opengraph_apple_touch_icon) {
                // Add apple touch icon
                $GLOBALS['TL_HEAD'][] = self::appleTouchIcon($filesModel->path);
                $GLOBALS['TL_HEAD'][] = self::appleTouchIcon($filesModel->path, 72);
                $GLOBALS['TL_HEAD'][] = self::appleTouchIcon($filesModel->path, 114);
                $GLOBALS['TL_HEAD'][] = self::appleTouchIcon($filesModel->path, 144);
            }

            if($objRootPage->opengraph_favicon) {
                
                $ext       = str_replace('jpg', 'jpeg', $filesModel->extension);
                $imageType = in_array($ext, array('png','gif','jpeg')) ? ' type="image/'.$ext.'"' : '';

                $GLOBALS['TL_HEAD'][] = sprintf(
                    '<link rel="icon" href="%s"%s>',
                    \Image::get($filesModel->path, 64, 64,'center_center'),
                    $imageType
                );

                // Old *.ico format
                if (!file_exists(TL_ROOT . '/favicon.ico')) {
                    self::generateFavicon($filesModel->path);
                }
                $GLOBALS['TL_HEAD'][] = '<link rel="shortcut icon" href="//'.\Environment::get('host').'/favicon.ico">';
            }

            // TODO <link rel="apple-touch-startup-image" href="images/startup.png" />

		}

		// TODO $objPage->opengraph_type;



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

    public static function generateFavicon($imgPath) {

        $floIcon      = new floIcon();
        $arrIconSizes = array(16, 24, 32, 64, 128, 256);
        $imgSize      = getimagesize(TL_ROOT . '/' . $imgPath);

        foreach ($arrIconSizes as $iconsize) {
            if ($imgSize[0] >= $iconsize && $imgSize[1] >= $iconsize) {
                $src = \Image::get($imgPath, $iconsize, $iconsize , 'center_center');

                try {
                    // add file to ICO file, try PNG, JPG and GIF in order
                    if (
                        $image = @imagecreatefrompng(TL_ROOT . '/'. $src) or
                        $image = @imagecreatefromjpeg(TL_ROOT . '/'. $src) or
                        $image = @imagecreatefromgif(TL_ROOT . '/'. $src)
                    )
                    {
                        $floIcon->addImage($image, 32);
                    }
                }
                catch (Exception $e) {}
            }
        }
        $objFile = new \File('favicon.ico');
        $objFile->write($floIcon->formatICO());
        $objFile->close();
        return true;
    }


    private static function appleTouchIcon($path, $dim = 57) {
        $img   = \Image::get($path, $dim, $dim, 'center_center');
        $sizes = ($dim == 57) ? '' : 'sizes="'.$dim.'x'.$dim.'" ';
        return '<link rel="apple-touch-icon" '.$sizes.'href="'.$img.'" />';
    }
}



