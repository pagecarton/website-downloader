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
			if( ! $values = $this->getForm()->getValues() ){ return false; }
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
            //var_export( $data );
            //  Code that runs the widget goes here...
            $baseUrl = self::getBaseUrl( $data );
            $baseDir = self::getBaseDir( $baseUrl );
            set_time_limit( 0 );
            $done = null;
            foreach( $data['pages'] as $link )
            {
                //break;

                $localUrl = self::getLocalURL( $link, $data );
                $localUrl = static::filterHtmlLocalLink( $localUrl );
                $localFile = $baseDir . DS . $localUrl;    
                $myUrl = Ayoola_Application::getUrlPrefix() . $baseUrl . '/' . $localUrl;

                if( is_file( $localFile ) )  
                {
                    $done .= '<div class="pc-notify-info">Already Saved <a target="_blank" href="' . $myUrl . '">' . $myUrl . '</a></div>';
                    continue;
                }
                Ayoola_Doc::createDirectory( dirname( $localFile ) );
                $content = self::getContent( $link );   
                $content = static::filterContent( $data, $content );
                file_put_contents( $localFile, $content );
                $done .= '<div class="pc-notify-info">Saved <a target="_blank" href="' . $myUrl . '">' . $myUrl . '</a></div>';
            }
            foreach( $data['links_to_download'] as $link )
            {
                $localUrl = self::getLocalURL( $link, $data );
                $localFile = $baseDir . DS . $localUrl;
                if( ! stripos( $localFile, '.css' ) && ! stripos( $localFile, '.js' ))
                {
                //    continue;
                }

                if( is_file( $localFile ) )
                {
                    continue;
                }
                Ayoola_Doc::createDirectory( dirname( $localFile ) );
                $content = self::getContent( $link );
                $content = static::filterContent( $data, $content );
                file_put_contents( $localFile, $content );
            }

            return $done;
             // end of widget process
          
		}  
		catch( Exception $e )
        { 
            //  Alert! Clear the all other content and display whats below.
            return false; 
        }
	}
	// END OF CLASS
}
