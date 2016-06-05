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
            'og:site_name' => false,

            'og:title' => false,

            'og:description' => false,

            'og:url' => false,

            'og:image' => false,

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
     * @param String $tagContent The metatag content, e.g. 'og:title'
     * @param String $tagValue   The metatag value, e.g. 'Example Website Name'
     * @return void
     */
    public function addTag($tagContent, $tagValue) {
        if ($this->existingTags === false) {
            $this->checkExistingTags();
        }
        if ($this->existingTags[$tagContent]) {
            return;
        }
        $this->existingTags[$tagContent] = true;

        if(!is_array($GLOBALS['TL_HEAD'])) {
            $GLOBALS['TL_HEAD'] = array();
        }

        $tagContent = implode(array_map('ucfirst', explode(':', $tagContent)));
        $tagContent = implode(array_map('ucfirst', explode('_', $tagContent)));
        $GLOBALS['TL_HEAD'][] = OpenGraph::{'get' . $tagContent . 'Tag'}($tagValue);
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
     * Adds all the missing OpenGraph tags to the head section.
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
        $this->addTag('og:site_name', $objPage->rootTitle);
        $this->addTag('og:title', $objPage->pageTitle);
        $this->addTag('og:description', $objPage->description);

        $url = \Environment::get('base').$this->generateFrontendUrl($objPage->row());
        $this->addTag('og:url', $url);

        //$this->addTag('og:locale', '');
        // TODO $objPage->opengraph_type;


        // Sollen die Bilder der pageimage Erweiterung benutzt werden
        $usePageImage = ($objRootPage->opengraph_pageimage === '1') && (in_array('pageimage', \ModuleLoader::getActive()));

        // Wurde schon ein Bild eingefügt?
        if (!$this->existingTags['og:image']) {

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
     * Adds all the missing OpenGraph tags, which can be inferred from the article entry to the head section.
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

        if ($articleRow['addImage'] === '1') {
            $filesModel = \FilesModel::findByUuid($articleRow['singleSRC']);
            $this->addOpenGraphImage($filesModel);
        }
    }

    /**
     * Add OpenGraph image metatags to the page.
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
    }
}
