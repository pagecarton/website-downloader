<?php

/**
 * PageCarton Content Management System
 *
 * LICENSE
 *
 * @category   PageCarton CMS
 * @package    PC_WebsiteGrabber_Project_Grab
 * @copyright  Copyright (c) 2017 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Grab.php Wednesday 20th of December 2017 08:14PM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Widget
 */

class PC_WebsiteGrabber_Project_Grab extends PC_WebsiteGrabber_Project_Abstract
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
			$this->createConfirmationForm( 'Grab', 'Grab Website' );
			$this->setViewContent( $this->getForm()->view(), true );
      //      var_export( $data );
			if( ! $values = $this->getForm()->getValues() ){ return false; }
        //    ;
		    $this->setViewContent( self::grab( $data ) );
            $baseUrl = self::getBaseUrl( $data );
            $baseDir = self::getBaseDir( $baseUrl );
			$this->setViewContent( '<div class="goodnews">Website contents successfully saved locally to ' . $baseDir . '</div>' );
        }
		catch( Exception $e )
        { 
            //  Alert! Clear the all other content and display whats below.
            $this->setViewContent( '<p class="badnews">Theres an error in the code</p>', true ); 
            return false; 
        }
    }
    
    /**
     * Performs the whole widget running process
     * 
     */
	public static function grab( $data )
    {    
		try
		{ 
            //  Code that runs the widget goes here...
            $baseUrl = self::getBaseUrl( $data );
            $baseDir = self::getBaseDir( $baseUrl );
            set_time_limit( 0 );
            $done = null;
            foreach( $data['pages'] as $link )
            {
                $localUrl = self::getLocalURL( $link );
                $localUrl = static::filterHtmlLocalLink( $localUrl );
                $localFile = $baseDir . DS . $localUrl;
                if( is_file( $localFile ) )
                {
                    continue;
                }
                $myUrl = Ayoola_Application::getUrlPrefix() . $baseUrl . '/' . $localUrl;
                Ayoola_Doc::createDirectory( dirname( $localFile ) );
                $storage = self::getObjectStorage( array( 'id' => 'content-' . md5( $link ), 'device' => 'File' ) );
       //         var_export( $localUrl );
       //         var_export( $localFile );
                if( ! $content = $storage->retrieve() )
                {
                    $content = self::fetchLink( $link, array( 'time_out' => 288000, 'connect_time_out' => 288000, 'return_error_response' => true ) );
                    $storage->store( $content );
                }
                $content = static::filterContent( $data, $content );
                file_put_contents( $localFile, $content );
                $done .= '<div class="pc-notify-info">Saved <a target="_blank" href="' . $myUrl . '">' . $myUrl . '</a></div>';
		//	    $this->setViewContent( '<div class="pc-notify-info">Saved <a target="_blank" href="' . $myUrl . '">' . $myUrl . '</a></div>' );
              //  echo $content;
              //  exit();
             //   break;
            }
            foreach( $data['links_to_download'] as $link )
            {
                $localUrl = self::getLocalURL( $link );
                $localFile = $baseDir . DS . $localUrl;
           //     var_export( $link );
            //    var_export( $localUrl );
                if( is_file( $localFile ) )
                {
                    continue;
                }
                Ayoola_Doc::createDirectory( dirname( $localFile ) );
                $storage = self::getObjectStorage( array( 'id' => 'content-' . md5( $link ), 'device' => 'File' ) );
                if( ! $content = $storage->retrieve() )
                {
                    $content = self::fetchLink( $link, array( 'time_out' => 288000, 'connect_time_out' => 288000, 'return_error_response' => true ) );
                    $storage->store( $content );
                }
                $content = static::filterContent( $data, $content );
                file_put_contents( $localFile, $content );
            }

            return $done;
             // end of widget process
          
		}  
		catch( Exception $e )
        { 
            //  Alert! Clear the all other content and display whats below.
         //   $this->setViewContent( '<p class="badnews">Theres an error in the code</p>', true ); 
            return false; 
        }
	}
	// END OF CLASS
}
