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


$GLOBALS['TL_DCA']['tl_page']['palettes']['__selector__'][]      = 'opengraph_enable';


$GLOBALS['TL_DCA']['tl_page']['palettes']['root']               .= ';{opengraph_legend:hide},opengraph_enable';
$GLOBALS['TL_DCA']['tl_page']['palettes']['regular']            .= ';{opengraph_legend:hide},opengraph_image,opengraph_twitter_card';

$GLOBALS['TL_DCA']['tl_page']['subpalettes']['opengraph_enable'] = 'opengraph_twitter_site,opengraph_twitter_card,opengraph_img_recursive,opengraph_pageimage,opengraph_image';

$GLOBALS['TL_DCA']['tl_page']['config']['onload_callback'][]     = array('tl_page_opengraph', 'adjustDca');



$GLOBALS['TL_DCA']['tl_page']['fields']['opengraph_enable'] = array(
    'label'                        => &$GLOBALS['TL_LANG']['tl_page']['opengraph_enable'],
    'inputType'                 => 'checkbox',
    'eval'                      => array('tl_class'=>'m12', 'submitOnChange' => true),
    'sql'                       => "char(1) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_page']['fields']['opengraph_twitter_site'] = array(
    'label'                   => &$GLOBALS['TL_LANG']['tl_page']['opengraph_twitter_site'],
    'inputType'               => 'text',
    'eval'                    => array('tl_class'=>'w50'),
    'sql'                     => "varchar(64) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_page']['fields']['opengraph_twitter_card'] = array(
    'label'                   => &$GLOBALS['TL_LANG']['tl_page']['opengraph_twitter_card'],
    'inputType'               => 'select',
    'options'                 => array('', 'summary', 'summary_large_image', 'photo', 'gallery'),
    'default'                 => '',
    'eval'                    => array('tl_class'=>'w50'),
    'sql'                     => "varchar(24) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_page']['fields']['opengraph_pageimage'] = array(
    'label'                        => &$GLOBALS['TL_LANG']['tl_page']['opengraph_pageimage'],
    'inputType'                 => 'checkbox',
    'eval'                      => array('tl_class'=>'w50 m12', 'submitOnChange' => true),
    'sql'                       => "char(1) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_page']['fields']['opengraph_img_recursive'] = array(
    'label'                        => &$GLOBALS['TL_LANG']['tl_page']['opengraph_img_recursive'],
    'inputType'                 => 'checkbox',
    'eval'                      => array('tl_class'=>'w50 m12'),
    'sql'                       => "char(1) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_page']['fields']['opengraph_image'] = array(
    'label'                     => &$GLOBALS['TL_LANG']['tl_page']['opengraph_image'],
    'exclude'                    => true,
    'inputType'                    => 'fileTree',
    'eval'                        => array('extensions' => 'png,gif,jpg,jpeg', 'files' => true, 'fieldType' => 'radio'),
    'sql'                       => "binary(16) NULL",
);


class tl_page_opengraph extends tl_page {

    public function adjustDca() {
        $pageImageActive = in_array('pageimage', \ModuleLoader::getActive());
        if (!$pageImageActive) {
            $GLOBALS['TL_DCA']['tl_page']['subpalettes']['opengraph_enable'] = str_replace(
                'opengraph_pageimage,',
                '',
                $GLOBALS['TL_DCA']['tl_page']['subpalettes']['opengraph_enable']
            );
        }

        if ('edit' === Input::get('act') && $pageImageActive) {
            $pageObj = \PageModel::findByPk(Input::get('id'));
            if ($pageObj && $pageObj->opengraph_pageimage === '1') {
                $GLOBALS['TL_DCA']['tl_page']['subpalettes']['opengraph_enable'] = str_replace(
                    ',opengraph_image',
                    '',
                    $GLOBALS['TL_DCA']['tl_page']['subpalettes']['opengraph_enable']
                );
            }

            $objRootPage = \PageModel::findByPk($pageObj->rootId);
            if ($objRootPage && $objRootPage->opengraph_pageimage === '1') {
                $GLOBALS['TL_DCA']['tl_page']['palettes']['regular'] = str_replace(
                    ';{opengraph_legend:hide},opengraph_image',
                    '',
                    $GLOBALS['TL_DCA']['tl_page']['palettes']['regular']
                );
            }

        }

    }


}

