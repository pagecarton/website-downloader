<?php

/**
 * PageCarton Content Management System
 *
 * LICENSE
 *
 * @category   PageCarton CMS
 * @package    PC_WebsiteGrabber_Project_Abstract
 * @copyright  Copyright (c) 2018 PageCarton (http://www.pagecarton.org)
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @version    $Id: Abstract.php Tuesday 10th of July 2018 07:59AM ayoola@ayoo.la $
 */

/**
 * @see PageCarton_Widget
 */


class PC_WebsiteGrabber_Project_Abstract extends PageCarton_Widget
{
	
    /**
     * Identifier for the column to edit
     * 
     * @var array
     */
	protected $_identifierKeys = array( 'project_id' );
 	
    /**
     * The column name of the primary key
     *
     * @var string
     */
	protected $_idColumn = 'project_id';
	
    /**
     * Identifier for the column to edit
     * 
     * @var string
     */
	protected $_tableClass = 'PC_WebsiteGrabber_Project';
	
    /**
     * 
     * 
     * @var array
     */
	protected static $_linkRegex = '#(href|src|url)[\s]*[=|\(][\s]*["\']?([^\'"\(\)]*)["\']?#';
	
    /**
     * 
     * 
     * @var array
     */
	protected static $_linkRegexFull = '#["\']((http|https)://([^\'"\(\)\\\\]*))["\']?#';
	
    /**
     * Access level for player. Defaults to everyone
     *
     * @var boolean
     */
	protected static $_accessLevel = array( 99, 98 );


    /**
     * 
     */
	public static function getContent( $url )  
    {
        $storage = self::getObjectStorage( array( 'id' => 'grabbed-website-x' . $url, 'device' => 'File' ) );
        if( ! $content = $storage->retrieve() )
        {
            $content = self::fetchLink( $url, array( 'time_out' => 288000, 'connect_time_out' => 288000, 'return_error_response' => true ) );
            $storage->store( $content );
        }
        return $content;
    }


    /**
     * 
     */
	public static function getBaseUrl( $data )  
    {
        $urlInfo = parse_url( $data['website'] );
        $baseUrl = '/Grabbed-Websites' . '/' . ( $urlInfo['host'] ? : $data['website'] ) . '/' . ( $data['modified_time'] ? : $data['creation_time'] );
        return $baseUrl;
    }

    /**
     * 
     */
	public static function getBaseDir( $baseUrl )  
    {
        $baseDir = Ayoola_Doc_Browser::getDocumentsDirectory() . $baseUrl;
        return $baseDir;
    }

    /**
     * 
     */
	public static function filterHtmlLocalLink( $link )  
    {
        $localUrl = $link;
        if( strpos( $localUrl, '.xyz' ) !== false )
        {
            $localUrl = str_ireplace( '.xyz', '', $localUrl );
        }
        if( empty( $localUrl ) )
        {
            $localUrl .=  'index';
        }
        if( strpos( $localUrl, '.html' ) === false )
        {
            $localUrl .= '.html';
        }
        return $localUrl;
    }

    /**
     * 
     */
	public static function setLocalLinks( & $values )  
    {
        $storage = static::getObjectStorage( array( 'id' => 'local-links' . $values['website'], 'device' => 'Session' ) );

        $values['local_links'] = $storage->retrieve();



        //  turn pages to .html
        foreach( $values['pages'] as $link )
        {

            $localLink = self::getLocalURL( $link, $values );
            $found = array_keys( $values['local_links'], $localLink );
            foreach( $found as $occur )
            {
                $localUrl = $values['local_links'][$occur];
                $localUrl = static::filterHtmlLocalLink( $localUrl );
                $values['local_links'][$occur] = $localUrl;

                if( strpos( $localUrl, '.xyz' ) !== false )
                {
                    $localUrl = str_ireplace( '.xyz', '', $localUrl );
                    $values['local_links'][$occur] = $localUrl;
                }
                if( empty( $localUrl ) )
                {
                    $localUrl .=  'index';
                }
                if( ! strpos( $localUrl, '.' ) )
                {
                    $localUrl .= '.html';
                    $values['local_links'][$occur] = $localUrl;
                }
            }
        }
    }

    /**
     * 
     */
	public static function getLocalURL( $link, array $data = null, $defaultExtension = 'xyz' )  
    {

        $xc = explode( '//', $data['website'] );
        $home = array_pop( $xc );
        $home = trim( $home, ' /' );


        if( stripos( $link, $home ) !== false )
        {
            //  fix
            //  templates.envytheme.com/poxo/default/index.html
            $home = self::filterHomePath( $home );
            $localUrl = str_ireplace( $home, '', $link );
        }
        else
        {
            $urlInfo = parse_url( $link );
            $localUrl = trim( $urlInfo['path'], '/' );
            if( empty( $urlInfo['host'] ) )
            {
                $localArray = explode( '/', $link );
                array_shift( $localArray );
                $localUrl = implode( '/', $localArray );
            }
        }
        
        if( strpos( $localUrl, '.php' ) )
        {
            $localUrl .= '.html';
        }
        list( $name, $extension ) = explode( '.', $localUrl );
        if( empty( $extension ) )
        {
            $extension = $defaultExtension;
            $localUrl .= '.' . $extension;
        }
        if( strpos( $extension, '?' ) )
        {
            list( $extension, $query ) = explode( '?', $extension );
            $name .= '-' . $query;
            $localUrl = $name . '.' . $extension;
        }
        if( strpos( $extension, '&' ) )
        {
            list( $extension, $query ) = explode( '&', $extension );
            $name .= '-' . $query;
            $localUrl = $name . '.' . $extension;
        }
        if( strlen( $name ) > 150 )
//      if( strlen( $name ) > 50 && $extension != 'html' )
        {
            $name =  md5( $name );
            $localUrl = $name;
            $localUrl .= $extension ? ( '.' . $extension ) : null;
        }
        $localUrl = str_replace( array( '?', '&', '--' ), '-', $localUrl );
        $localUrl = trim( $localUrl, ' /-' );
        
        if( $extension === 'html' )
        {
            $localUrl = str_replace( array( '/' ), '-', $localUrl );
        }
        $localUrl = trim( $localUrl, ' /-?#' );
  
        return $localUrl;
    }

    /**
     * 
     */
	public static function filterContent( $projectData, $content )  
    {
        $ccc = function( $matches ) use ( $projectData )
        {
            $link = $matches[0];
            if( ! empty( $projectData['local_links'][$matches[2]] ) )
            {
                $link = str_replace( $matches[2], $projectData['local_links'][$matches[2]], $link );
            }
            return $link;
        };
        $xxx = function( $matches ) use ( $projectData )
        {
            $link = $matches[0];
            if( ! empty( $projectData['local_links'][$matches[1]] ) )
            {
                $link = str_replace( $matches[1], $projectData['local_links'][$matches[1]], $link );
            }
            return $link;
        };
        $content = preg_replace_callback( static::$_linkRegex, $ccc, $content  );
        $content = preg_replace_callback( static::$_linkRegexFull, $xxx, $content  );

        foreach( $projectData['external_sites_to_download'] as $domain )
        {
            $search = array
            ( 
                $projectData['website'],
                $domain,
            );
            $content = str_ireplace( $search, 'example.com', $content  );
        }
        return $content;
    }

    /**
     * 
     */
	public static function filterDomainName( $domain )  
    {
        $domain =  str_ireplace( 'www.', '', $domain );
        $domain =  strtolower( $domain );
        return $domain;
    }

    /**
     * 
     */
	public static function getLinkDomain( $link )  
    {
        $urlInfo = parse_url( $link );
        //         var_export( $urlInfo );
        if( empty( $urlInfo['host'] ) )
        {
            //    var_export( $urlInfo['path'] );
            if( strpos( $urlInfo['path'], '//' ) !== false )
            {
                $urlInfo['host'] = array_shift( explode( '/', array_pop( explode( '//', $urlInfo['path'] ) ) ) );
            //    var_export(  $urlInfo['host'] );
            }
            else
            {
                $urlInfo['host'] = array_shift( explode( '/', $urlInfo['path'] ) );
            }
        }
        $urlInfo['host'] = self::filterDomainName( $urlInfo['host'] );
        if( ! empty( $urlInfo['host'] ) )
        {
        //      var_export( $this->getGlobalValue( 'external_sites_to_download' ) );
            if( ! in_array( $urlInfo['host'], Ayoola_Form::getGlobalValue( 'external_sites_to_download' ) ? : array() ) )
            {
        //        return false;
            }
        }
        return $urlInfo['host'];
    }

    /**
     * 
     * 
     * param string html content 
     * param string Home path e.g. http://example.com
     * return arrat 
     */
	public static function getLinks( $content, $homePath )  
    {
        preg_match_all( static::$_linkRegex, $content, $matches ); 
        preg_match_all( static::$_linkRegexFull, $content, $fullLinks ); 
        $fullLinks[1] = $fullLinks[1] ? : array();
        $matches[2] = $matches[2] ? : array();
        $storage = static::getObjectStorage( array( 'id' => 'local-links' . $homePath, 'device' => 'Session' ) );
        $matches[2] = array_merge( $matches[2], $fullLinks[1] );
        $matches[2] = array_unique( $matches[2] );
        $links = array();
        $formerLinks = array();
        $localLinks = array();
        foreach( $matches[2] as $key => $link )
        {
            //  set this
            $link = trim( $link );

            if( ! $link || $link[0] == '#' || $link[0] == '?' ){ continue; }
            if( in_array( $link, $links ) ){ continue; } // skip duplicate process

            $link = self::relativeLinkToFullPath( $link, $homePath );

            if( ! in_array( self::getLinkDomain( $link ), Ayoola_Form::getGlobalValue( 'external_sites_to_download' ) ? : array() ) )
            {
                continue;
            }
            $link = trim( $link, ' ' );
            $links[$link] = $link;
            $formerLinks[$matches[2][$key]] = $link;
            $localLinks[$matches[2][$key]] = self::getLocalURL( $link, array( 'website' => $homePath ) );
        }
        $storage->store( $localLinks );
        return $formerLinks;
    }

    /**
     * Convert http://example.com/url/index.htm to http://example.com/url
     * 
     * @param string Home path e.g. http://example.com
     * @return string 
     */
	public static function filterHomePath( $homePath )  
    {
        $urlInfoX = parse_url( 'https://' . $homePath );
        $dcY = trim( $urlInfoX['path'], '/' );
        $dcx = explode( '.', $dcY );
        $ext = array_pop( $dcx );


        if( count( $dcx ) > 0 )
        {
            $homePath = dirname( $homePath );
        }
        return $homePath;
    }

    /**
     * Convert /url to http://example.com/url
     * 
     * @param string Url e.g. /url
     * @param string Home path e.g. http://example.com
     * @return string 
     */
	public static function relativeLinkToFullPath( $link, $homePath )  
    {

        $homePath = self::filterHomePath( $homePath );

        $homePath = trim( $homePath, ' /' );
        if( $link[0] == '/' && ! strstr( $link, '//' ) )
        { 
            $link = '//' . $homePath . $link; 
        } //	Seek for absolute urls
        while( ! strstr( $link, '//' ) && ! strstr( $link, ':' ) ) // Seek for relative urls			
        {
            $baseUrl = trim( $homePath, '' ) . '';
            switch( $link[0] )
            {
                case '#':
                case '?':
                  //  $link = $baseUrl . $link;
                 //   continue 2; //	Means we are dealing with the same url
            }
            if( stripos( $link, 'javascript:' ) === 0 || stripos( $link, 'mailto:' ) === 0 )
            {
                return false; //	Means we are dealing with the same url
            }
            $link = str_replace( '../', '', $link, $count );
            do
            { 
                if( $count < 1 )
                { 
                    //  href="link" = href="http://example.com/link"
   //     var_export( $link . '<br>' );
   //     var_export( $homePath . '<br>' );
            //        var_export( $homePath );
                    $link = $homePath . '/' . $link; 
   //     var_export( $link . '<br>' );
                    break 2; 
                }
                $baseUrl = dirname( $baseUrl ) . '/';
                if( ! strstr( $baseUrl, '//' ) )
                { 
                    break 2; 
                }
                --$count;
            }
            while( true );
            
            $link = ltrim( $link, './' );
            $stringLength = strlen( $baseUrl ) - 1;
            if( $baseUrl[$stringLength] != '/' ){ $baseUrl = dirname( $baseUrl ) . '/'; }
            $link = $baseUrl . $link;
            break;
        }
        if( ! strstr( $link, '//' ) )
        { 
            return false; 
        }
        $link = trim( $link, ' /' );
   //     var_export( $link . '<br>' );
     //   $link = self::filterDomainName( $link );
        $link =  str_ireplace( 'www.', '', $link );
        $link = array_pop( explode( '//', $link ) );
        return $link;
    }

    /**
     * creates the form for creating and editing page
     * 
     * param string The Value of the Submit Button
     * param string Value of the Legend
     * param array Default Values
     */
	public function createForm( $submitValue = null, $legend = null, Array $values = null )  
    {
		//	Form to create a new page
        $form = new Ayoola_Form( array( 'name' => $this->getObjectName(), 'data-not-playable' => true ) );
		$form->submitValue = $submitValue ;

		$fieldset = new Ayoola_Form_Element;
        $fieldset->addElement( array( 'name' => 'website', 'label' => 'Web Addresss', 'type' => 'InputText', 'value' => @$values['website'] ) ); 

        if( $homePath = $this->getGlobalValue( 'website' ) )
        {
            $homePath =  str_ireplace( 'www.', '', $homePath );
            $fieldset->addFilter( 'website', array( 'DefiniteValue' => $homePath ) ); 

            set_time_limit( 0 );
            $content = self::getContent( $homePath );

		    preg_match_all('#(//)(www\.)?([^/:"\'<>\s\\\\]*[\.][^/:"\'<>\s\\\\]*)(/)?#', $content, $matches ); 
            $matches[3] = $matches[3] ? : array();
            array_unshift( $matches[3], str_ireplace( 'www.', '', self::getLinkDomain( $homePath ) ) );
            $external = array_unique( array_map( 'strtolower', $matches[3] ) );
            $external = array_combine( $external, $external );
            $fieldset->addElement( array( 'name' => 'external_sites_to_download', 'label' => 'External sites to download assets from', 'type' => 'SelectMultiple', 'value' => @$values['external_sites_to_download'] ), $external ); 

            //	Look for html links
            $xml = new Ayoola_Xml();
            @$xml->loadHTML( $content );
            $childLinks = $xml->getElementsByTagName( 'a' );
                
		    $linksBank = array(); //	Tackle duplicate processing of links
		    $pages = array(); //	Tackle duplicate processing of links
            foreach( $childLinks as $each )
            {
                if( ! $link = $each->getAttribute( 'href' ) ){ continue; }

                //	Optimizing - remove hash and querystrings
                list( $link ) = explode( '#', $link );
                if( ! $link ){ continue; }
                if( in_array( $link, $linksBank ) ){ continue; } // skip duplicate process
                $linksBank[] = $link;
                $link = self::relativeLinkToFullPath( $link, $homePath );
                $linkDomain = self::getLinkDomain( $link );
                if( $linkDomain && ! in_array( $linkDomain, Ayoola_Form::getGlobalValue( 'external_sites_to_download' ) ? : array() ) )
                {
                    continue;
                }
                if( ! $link ){ continue; }
                $pages[$link] = $link;
            } 
            if( $pages )
            {
                unset( $pages[''] );
                ksort( $pages );
                $fieldset->addElement( array( 'name' => 'pages', 'label' => 'Pages to save', 'type' => 'SelectMultiple', 'value' => @$values['pages'] ? : $pages  ), $pages ); 
            }
            else
            {
                $fieldset->addElement( array( 'name' => 'pages', 'type' => 'Hidden', 'value' => null  ) ); 
            }

            if( $selectedPages = $this->getGlobalValue( 'pages' ) )
            {
                foreach( $selectedPages as $page )
                {
                    $content .= self::getContent( $page );  
                }

                if( $links = self::getLinks( $content, $homePath ) )
                {
                    $links = array_unique( array_combine( $links, $links ) );
                    unset( $links[''] );
                    ksort( $links );
                    $links = array_diff( $links, $pages );
                    $fieldset->addElement( array( 'name' => 'links_to_download', 'label' => 'Other Assets', 'type' => 'SelectMultiple', 'value' => @$values['links_to_download'] ? : $links ), $links ); 
                }
                else
                {
                    $fieldset->addElement( array( 'name' => 'links_to_download', 'type' => 'Hidden', 'value' => null  ) ); 
                }
            }

            $fieldset->addRequirements( array( 'NotEmpty' => null ) ); 
        }

		$fieldset->addLegend( $legend );
		$form->addFieldset( $fieldset );   
		$this->setForm( $form );
    } 

	// END OF CLASS
}
