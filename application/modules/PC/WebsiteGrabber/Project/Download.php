<?php

/**
 * PageCarton Content Management System
 *
 * LICENSE
 *
 * @category   PageCarton CMS
 * @package    PC_WebsiteGrabber_Project_Download
 * @copyright  Copyright (c) 2017 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Dowload.php Wednesday 20th of December 2017 08:14PM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Widget
 */

class PC_WebsiteGrabber_Project_Download extends PC_WebsiteGrabber_Project_Grab
{

    /**
     * Performs the whole widget running process
     * 
     */
	public function init()
    {    
		try
		{ 
            //  Code that runs the widget goes here...
			if( ! $data = $this->getIdentifierData() ){ return false; }
            $baseUrl = self::getBaseUrl( $data );
            $baseDir = self::getBaseDir( $baseUrl );
       //     var_export( $baseDir );
            if( ! is_dir( $baseDir ) )
            {
                self::grab( $data );
            }

            $filename = sys_get_temp_dir() . DS . str_replace( array( '.', ':', '/', ), '-', $data['website'] ) . '.tar';
            
            //	remove previous files
            @unlink( $filename );
            @unlink( $filename . '.gz' );

            $phar = 'Ayoola_Phar_Data';
            $export = new $phar( $filename  );
            $export->startBuffering();  
            $export->buildFromDirectory( $baseDir );
            $export->stopBuffering();
            
            $export->compress( Ayoola_Phar::GZ ); 
            unset( $export );
            $phar::unlinkArchive( $filename );
            
            //	download
            header( 'Content-Type: application/x-gzip' . '' );
            $document = new Ayoola_Doc( array( 'option' => $filename . '.gz' ) ); 
            $document->download();
            exit;

            

             // end of widget process
          
		}  
		catch( Exception $e )
        { 
            //  Alert! Clear the all other content and display whats below.
            $this->setViewContent( $e->getMessage() ); 
            $this->setViewContent( '<p class="badnews">Theres an error in the code</p>' ); 
            return false; 
        }
	}
	// END OF CLASS
}
