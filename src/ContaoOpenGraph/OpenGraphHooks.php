<?php

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2016 Leo Feyer
 *
 *
 * PHP version 5
 * @copyright  Martin Kozianka 2012-2016 <http://kozianka.de/>
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
        'width'  => 1200,
        'height' => 630,
        'ratio'  => 1.91 // (width/height)
    ];

    /**
     * Keep track of which tags already exist in the page
     * @var array
     */
    private $existingTags = false;

    /**
     * Checks which tags exist
     * @return void
     */
    private function checkExistingTags() {
        $this->existingTags = array(
            'twitter:card' => false,
            'twitter:site' => false,
            'og:site_name' => false,

            'og:title' => false,
            'twitter:title' => false,

            'og:description' => false,
            'twitter:description' => false,

            'og:url' => false,
            'twitter:url' => false,

            'og:image' => false,
            'twitter:image' => false,

            'og:locale'      => false,
        );

        if(is_array($GLOBALS['TL_HEAD'])) {
            foreach ($GLOBALS['TL_HEAD'] as $head) {
                foreach ($this->existingTags as $tagName => $tagExists) {
                    if(strpos($head, $tagName) > 0) $this->existingTags[$tagName] = true;
                }
            }
        }
    }

    /**
     * This method adds the given social media metatag to the head, if not already defined
     * 
     * @param String $tagProperty The metatag property, e.g. 'og:title'
     * @param String $tagValue   The metatag value, e.g. 'Example Website Name'
     * @return void
     */
    public function addTag($tagProperty, $tagValue) {
        if ($this->existingTags === false) {
            $this->checkExistingTags();
        }
        if ($this->existingTags[$tagProperty]) {
            return;
        }
        $this->existingTags[$tagProperty] = true;

        if(!is_array($GLOBALS['TL_HEAD'])) {
            $GLOBALS['TL_HEAD'] = array();
        }
        $GLOBALS['TL_HEAD'][] = sprintf('<meta property="%s" content="%s"/>', $tagProperty, htmlspecialchars($tagValue));
    }

    /**
     * If enabled, add the correct OpenGraph namespace declaration
     */
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

    /**
     * Adds all the missing OpenGraph and Twitter Card metatags to the head section.
     * Triggered by the generatePage hook.
     * 
     * @param \PageModel   $objPage        The page object
     * @param \LayoutModel $objLayout      The page layout
     * @param \PageRegular $objPageRegular The page regualr object
     * @return void
     */
    public function addOpenGraphTags(\PageModel $objPage, \LayoutModel $objLayout, \PageRegular $objPageRegular) {

        $objRootPage = \PageModel::findByPk($objPage->rootId);

        if ($objRootPage->opengraph_enable !== '1') {
            // OpenGraph not enabled in root page
            return;
        }

        // add metatags if missing
        $this->addTag('twitter:card', ($objPage->opengraph_twitter_card ?: $objRootPage->opengraph_twitter_card) ?: 'summary_large_image');
        $this->addTag('twitter:site', $objRootPage->opengraph_twitter_site);
        $this->addTag('og:site_name', $objPage->rootTitle);
        $this->addTag('og:title', $objPage->pageTitle);
        $this->addTag('twitter:title', $objPage->pageTitle);
        $this->addTag('og:description', $objPage->description);
        $this->addTag('twitter:description', $objPage->description);

        $url = \Environment::get('base').$this->generateFrontendUrl($objPage->row());
        $this->addTag('og:url', $url);
        $this->addTag('twitter:url', $url);

        //$this->addTag('og:locale', '');
        // TODO $objPage->opengraph_type;


        // Sollen die Bilder der pageimage Erweiterung benutzt werden
        $usePageImage = ($objRootPage->opengraph_pageimage === '1') && (in_array('pageimage', \ModuleLoader::getActive()));

        // Wurde schon ein Bild eingefügt?
        if (!$this->existingTags['og:image'] || !$this->existingTags['twitter:image']) {

            if ($usePageImage) {
                $arrUuids   = deserialize($objPage->pageImage);
                $filesModel = (is_array($arrUuids)) ? \FilesModel::findByUuid($arrUuids[0]) : null;
            } else {
                $filesModel = \FilesModel::findByUuid($objPage->opengraph_image);
            }

            // Soll in der Seitenstruktur nach einem Bild gesucht werden?
            if ($objRootPage->opengraph_img_recursive === '1') {
                foreach (\PageModel::findParentsById($objPage->id) as $parent) {
                    if ($filesModel === null) {
                        if ($usePageImage) {
                            $arrUuids   = deserialize($parent->pageImage);
                            $filesModel = (is_array($arrUuids)) ? \FilesModel::findByUuid($arrUuids[0]) : null;
                        } else {
                            $filesModel = \FilesModel::findByUuid($parent->opengraph_image);
                        }
                    }
                }
            }
            $this->addOpenGraphImage($filesModel);
        }

    }

    /**
     * Adds all the missing OpenGraph and Twitter Card metatags, which can be inferred from the article entry to the head section.
     * Triggered by the parseArticles hook.
     * 
     * @param \FrontendTemplate $objTemplate The front end template instance for the news article (e.g. news_full).
     * @param array             $articleRow  The current news item database result.
     * @param \ModuleNews       $objModule   The news module instance (e.g. ModuleNewsList).
     * @return void
     */
    public function parseArticlesHook($objTemplate, $articleRow, $objModule) {
        global $objPage;

        if ($objModule->opengraph_enable !== '1') {
            return;
        }

        $objRootPage = \PageModel::findByPk($objPage->rootId);

        if ($objRootPage->opengraph_enable !== '1') {
            return;
        }

        // title and description is set by news reader module
        // news reader module overrides $objPage->pageTitle and $objPage->description
        $this->addTag('og:url', \Environment::get('base').$objTemplate->link);
        $this->addTag('twitter:url', \Environment::get('base').$objTemplate->link);

        if ($articleRow['addImage'] === '1') {
            $filesModel = \FilesModel::findByUuid($articleRow['singleSRC']);
            $this->addOpenGraphImage($filesModel);
        }
    }

    /**
     * Add OpenGraph and Twitter Card image metatags to the page.
     * 
     * @param \FilesModel $filesModel
     * @return void
     */
    public function addOpenGraphImage($filesModel) {

        if ($filesModel === null || !is_file(TL_ROOT . '/' . $filesModel->path)) {
            return false;
        }

        $arrResize = self::$arrResizeMode;
        $fileObj   = new \File($filesModel->path);

        // Do not enlarge the image
        if ($fileObj->width < $arrResize['width']) {
            $arrResize['height'] = floor($fileObj->width / $arrResize['ratio']);
            $arrResize['width']  = $fileObj->width;
        }

        $img = \Environment::get('base') . \Image::get($filesModel->path, $arrResize['width'], $arrResize['height'], 'crop');

        $this->addTag('og:image', $img);
        $this->addTag('og:image:width', $arrResize['width']);
        $this->addTag('og:image:height', $arrResize['height']);
        $this->addTag('twitter:image', $img);
    }
}
