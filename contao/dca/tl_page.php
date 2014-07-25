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

$GLOBALS['TL_DCA']['tl_page']['config']['onsubmit_callback'][]       = array('tl_page_opengraph', 'favicon');

$GLOBALS['TL_DCA']['tl_page']['palettes']['regular']                .= ';{opengraph_legend:hide}, opengraph_image';
$GLOBALS['TL_DCA']['tl_page']['palettes']['root']                   .= ';{opengraph_legend:hide},opengraph_enable,opengraph_size,opengraph_image,opengraph_apple_touch_icon,opengraph_favicon';



$GLOBALS['TL_DCA']['tl_page']['fields']['opengraph_enable'] = array(
    'label'		              => &$GLOBALS['TL_LANG']['tl_page']['opengraph_enable'],
    'inputType'               => 'checkbox',
    'eval'                    => array('tl_class'=>'w50 m12'),
    'sql'                     => "char(1) NOT NULL default ''"
);


$GLOBALS['TL_DCA']['tl_page']['fields']['opengraph_size'] = array(
    'label'                   => &$GLOBALS['TL_LANG']['tl_page']['opengraph_size'],
    'exclude'                 => true,
    'inputType'               => 'imageSize',
    'options'                 => $GLOBALS['TL_CROP'],
    'reference'               => &$GLOBALS['TL_LANG']['MSC'],
    'eval'                    => array('rgxp'=>'digit', 'nospace'=>true, 'helpwizard'=>true, 'tl_class'=>'w50'),
    'sql'                     => "varchar(64) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_page']['fields']['opengraph_apple_touch_icon'] = array(
    'label'		              => &$GLOBALS['TL_LANG']['tl_page']['opengraph_apple_touch_icon'],
    'inputType'               => 'checkbox',
    'eval'                    => array('tl_class'=>'w50 m12'),
    'sql'                     => "char(1) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_page']['fields']['opengraph_favicon'] = array(
    'label'		              => &$GLOBALS['TL_LANG']['tl_page']['opengraph_favicon'],
    'inputType'               => 'checkbox',
    'eval'                    => array('tl_class'=>'w50 m12'),
    'sql'                     => "char(1) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_page']['fields']['opengraph_image'] = array
(
    'label'						=> &$GLOBALS['TL_LANG']['tl_page']['opengraph_image'],
    'exclude'					=> true,
    'inputType'					=> 'fileTree',
    'eval'						=> array('extensions' => 'png,gif,jpg,jpeg', 'files' => true, 'fieldType' => 'radio'),
    'sql'                       => "binary(16) NULL",
);


class tl_page_opengraph {

    public function favicon(DataContainer $dc) {
        if ($dc->activeRecord->type == 'root' && $dc->activeRecord->opengraph_enable == '1'
            && $dc->activeRecord->opengraph_favicon == '1') {

            $filesModel = \FilesModel::findByUuid($dc->activeRecord->opengraph_image);
            if ($filesModel != null) {
                \OpenGraphHooks::generateFavicon($filesModel->path);
            }

        }
    }

}