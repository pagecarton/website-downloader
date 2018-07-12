<?php

/**
 * PageCarton Content Management System
 *
 * LICENSE
 *
 * @category   PageCarton CMS
 * @package    PC_WebsiteGrabber_Project_List
 * @copyright  Copyright (c) 2017 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: List.php Wednesday 20th of December 2017 03:21PM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Widget
 */

class PC_WebsiteGrabber_Project_List extends PC_WebsiteGrabber_Project_Abstract
{
 	
    /**
     * 
     * 
     * @var string 
     */
	  protected static $_objectTitle = 'Website Grabber Projects';   

    /**
     * Performs the creation process
     *
     * @param void
     * @return void
     */	
    public function init()
    {
      $this->setViewContent( $this->getList() );		
    } 
	
    /**
     * Paginate the list with Ayoola_Paginator
     * @see Ayoola_Paginator
     */
    protected function createList()
    {
		require_once 'Ayoola/Paginator.php';
		$list = new Ayoola_Paginator();
		$list->pageName = $this->getObjectName();
		$list->listTitle = self::getObjectTitle();
		$list->setData( $this->getDbData() );
		$list->setListOptions( 
								array( 
							//			'Sub Domains' => '<a rel="spotlight;" onClick="ayoola.spotLight.showLinkInIFrame( \'' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/Application_Domain_SubDomainList/\' );" title="">Sub Domains</a>',    
									) 
							);
		$list->setKey( $this->getIdColumn() );
		$list->setNoRecordMessage( 'No data added to this table yet.' );
		
		$list->createList
		(
			array(
                    'website' => array( 'field' => 'website', 'value' =>  '%FIELD%', 'filter' =>  '' ), 
                //    'links_to_download' => array( 'field' => 'links_to_download', 'value' =>  '%FIELD%', 'filter' =>  '' ), 
             //       'pages' => array( 'field' => 'pages', 'value' =>  '%FIELD%', 'filter' =>  '' ), 
            //        'external_sites_to_download' => array( 'field' => 'external_sites_to_download', 'value' =>  '%FIELD%', 'filter' =>  '' ), 
           //         '' => array( 'field' => '', 'value' =>  '%FIELD%', 'filter' =>  '' ), 
                    'Added' => array( 'field' => 'creation_time', 'value' =>  '%FIELD%', 'filter' =>  'Ayoola_Filter_Time' ), 
                    '  ' => '<a style="font-size:smaller;" rel="shadowbox;changeElementId=' . $this->getObjectName() . '" href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/PC_WebsiteGrabber_Project_Grab/?' . $this->getIdColumn() . '=%KEY%">Grab</a>', 
                    '   ' => '<a style="font-size:smaller;" rel="" href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/PC_WebsiteGrabber_Project_Download/?' . $this->getIdColumn() . '=%KEY%">Download</a>', 
                    '' => '%FIELD% <a style="font-size:smaller;" rel="shadowbox;changeElementId=' . $this->getObjectName() . '" href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/PC_WebsiteGrabber_Project_Editor/?' . $this->getIdColumn() . '=%KEY%">edit</a>', 
                    ' ' => '%FIELD% <a style="font-size:smaller;" rel="shadowbox;changeElementId=' . $this->getObjectName() . '" href="' . Ayoola_Application::getUrlPrefix() . '/tools/classplayer/get/object_name/PC_WebsiteGrabber_Project_Delete/?' . $this->getIdColumn() . '=%KEY%">x</a>', 
				)
		);
		return $list;
    } 
	// END OF CLASS
}
