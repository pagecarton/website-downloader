<?php

/**
 * PageCarton Content Management System
 *
 * LICENSE
 *
 * @category   PageCarton CMS
 * @package    PC_WebsiteGrabber_Project
 * @copyright  Copyright (c) 2018 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Project.php Tuesday 10th of July 2018 07:59AM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Table
 */


class PC_WebsiteGrabber_Project extends PageCarton_Table
{

    /**
     * The table version (SVN COMPATIBLE)
     *
     * @param string
     */
    protected $_tableVersion = '0.4';  

    /**
     * Table data types and declaration
     * array( 'fieldname' => 'DATATYPE' )
     *
     * @param array
     */
	protected $_dataTypes = array (
  'website' => 'INPUTTEXT',
  'links_to_download' => 'JSON',
  'pages' => 'JSON',
  'local_links' => 'JSON',
  'external_sites_to_download' => 'JSON',
);


	// END OF CLASS
}
