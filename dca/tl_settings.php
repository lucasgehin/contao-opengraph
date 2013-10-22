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

$GLOBALS['TL_DCA']['tl_settings']['palettes']['default'] .= ';{opengraph_legend},opengraph_enable,opengraph_size';

$GLOBALS['TL_DCA']['tl_settings']['fields']['opengraph_enable'] = array(
		'label'		              => &$GLOBALS['TL_LANG']['tl_settings']['opengraph_enable'],
		'inputType'               => 'checkbox',
		'eval'                    => array('tl_class'=>'w50 m12')
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['opengraph_size'] = array(
		'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['opengraph_size'],
		'exclude'                 => true,
		'inputType'               => 'imageSize',
		'options'                 => $GLOBALS['TL_CROP'],
		'reference'               => &$GLOBALS['TL_LANG']['MSC'],
		'eval'                    => array('rgxp'=>'digit', 'nospace'=>true, 'helpwizard'=>true, 'tl_class'=>'w50'),
);


