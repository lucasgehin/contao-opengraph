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

$GLOBALS['TL_DCA']['tl_page']['palettes']['regular']                .= ';{opengraph_legend:hide},opengraph_image';
$GLOBALS['TL_DCA']['tl_page']['palettes']['root']                   .= ';{opengraph_legend:hide},opengraph_enable,opengraph_image';

$GLOBALS['TL_DCA']['tl_page']['fields']['opengraph_enable'] = array(
    'label'		                => &$GLOBALS['TL_LANG']['tl_page']['opengraph_enable'],
    'inputType'                 => 'checkbox',
    'eval'                      => array('tl_class'=>'m12'),
    'sql'                       => "char(1) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_page']['fields']['opengraph_image'] = array(
    'label'                     => &$GLOBALS['TL_LANG']['tl_page']['opengraph_image'],
    'exclude'					=> true,
    'inputType'					=> 'fileTree',
    'eval'						=> array('extensions' => 'png,gif,jpg,jpeg', 'files' => true, 'fieldType' => 'radio'),
    'sql'                       => "binary(16) NULL",
);