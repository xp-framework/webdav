<?php namespace peer\webdav;

use peer\http\RequestData;
use peer\webdav\WebdavConnection;
use peer\Header;
use util\MimeType;

/**
 * WebDAV Client.
 * 
 * WebDAV stands for "Web-based Distributed Authoring and
 * Versioning". It is a set of extensions to the HTTP protocol
 * which allows users to collaboratively edit and manage files
 * on remote web servers.
 *
 * @see  http://www.webdav.org
 * @see  rfc://2518
 * @see  rfc://3253
 * @see  rfc://3648
 */
class WebdavClient extends \lang\Object {
  public 
    $url=     null,
    $uri=     '',
    $path=    '',
    $depth=   '1',
    $xml=     null,
    $source=        '',
    $destination=   '',
    $overwrite=     '1';   

  /**
   * Constructor.
   *
   * @param   var url either a string or a peer.URL object
   */
  public function __construct($url) {
    if (!$url instanceof URL) $this->url= new URL($url); else $this->url= $url;
  }
  
  /**
   * Get a Connection
   *
   * @param   string uri
   * @return  peer.webdav.WebdavConnection
   */
  public function getConnection($uri= null) {
    return new WebdavConnection(
      new URL($this->url->getURL().$this->path.($uri === null ? '' : '/'.$uri))
    );
  }
  
  /**
   * Helper Method to set the path
   *
   * @param   string path
   */
  public function setPath($path) {
    $this->path= $path;
  }
  
  /**
   * Do a Head Request to check if file exists
   *
   * @param   string uri, filename or directory
   * @return  peer.http.HttpResponse response object
   * @see     rfc://2518
   */
  public function exists($uri) {    
    return $this->getConnection($uri)->head();
  }
  
  /**
   * Do a Propfind on Webdav server
   *
   * @param   string uri, filename or directory
   * @param   string xml, The XML of the Propfind Request (e.g. to select properties)  
   * @param   int depth, default 1
   * @return  peer.http.HttpResponse response object
   * @see     rfc://2518
   */
  public function read($uri= null, $xml= null, $depth= '1') {     
    return $this->getConnection($uri)->propfind(
      $xml,
      array(
        new Header('Depth', $depth)
      )
    );
  }
  
  /**
   * Do a Put on Webdav server
   *
   * @param   io.File file
   * @param   string uri, filename or directory
   * @return  peer.http.HttpResponse response object
   * @see     rfc://2518
   */
  public function put($file, $uri= null) {  
    // If no uri or filename is specified, take the original filename  
    if ($uri === null) $uri= $file->getFilename();
          
    // Encode uri to handle files/directories containing spaces
    $uri= rawurlencode($uri);    
    
    if (!$file->isOpen()) $file->open(FILE_MODE_READ);
    return $this->getConnection($uri)->put(
      new RequestData($file->read($file->size())),
      array(
        new Header('Content-Type', MimeType::getByFilename($uri))
      )
    );
  }
  
  /**
   * Do a Get on Webdav server
   *
   * @param   string uri, filename or directory
   * @return  peer.http.HttpResponse response object
   * @see     rfc://2518
   */
  public function get($uri= null) {    
    return $this->getConnection($uri)->get();
  }
  
  /**
   * Do a Proppatch request
   *
   * @param   string xml, The XML Representation of the Properties
   * @param   string uri, filename or directory
   * @return  peer.http.HttpResponse response object
   * @see     rfc://2518
   */
  public function proppatch($xml, $uri= null) {          
    return $this->getConnection($uri)->proppatch($xml);    
  }
  
  /**
   * Do a MkCol Request
   *
   * @param   string uri, The uri of the new collection
   * @return  peer.http.HttpResponse response object
   * @see     rfc://2518
   */
  public function mkcol($uri) {        
    return $this->getConnection($uri)->mkcol();      
  }
      
  /**
   * Do a Copy Request
   *
   * @param   string source
   * @param   string destination
   * @param   bool overwrite, default false
   * @param   var depth, default Infinity
   * @return  peer.http.HttpResponse response object
   * @see     rfc://2518
   */
  public function copy($source, $destination, $overwrite= false, $depth= 'Infinity') {        
    return $this->getConnection($source)->copy(
      null,
      array(
        new Header('Overwrite', $overwrite ? 'T' : 'F'),
        new Header('Destination', $destination),
        new Header('Depth', $depth)
      )
    );
  }
  
  /**
   * Do a Move Request
   *
   * @param   string source
   * @param   string destination
   * @param   bool overwrite, default false
   * @return  peer.http.HttpResponse response object
   * @see     rfc://2518
   */
  public function move($source, $destination, $overwrite= false) {   
    return $this->getConnection($source)->move(
      null,
      array(
        new Header('Overwrite', $overwrite ? 'T' : 'F'),
        new Header('Destination', $destination)
      )
    );
  }
  
  /**
   * Do a Lock Request
   *
   * @param   string uri The uri of the collection or file
   * @param   string xml, The XML of the lockrequest
   * @return  peer.http.HttpResponse response object
   * @see     rfc://2518
   */
  public function lock($uri, $xml) {    
    return $this->getConnection($uri)->lock(
      $xml,
      array(
        new Header('Timeout', 'Infinity'),
        new Header('Content-Type', 'text/xml'),
        new Header('Content-Length', strlen($xml))
      )
    );
  }  
  
  /**
   * Do a Unlock Request
   *
   * @param   string uri, filename or directory
   * @param   string locktoken
   * @return  peer.http.HttpResponse response object
   * @see     rfc://2518
   */
  public function unlock($uri, $locktoken) {    
    return $this->getConnection($uri)->unlock(
      null,
      array(
        new Header('Lock-Token', $locktoken)
      )
    );
  }
  
  /**
   * Do a Delete Request
   *
   * @param   string uri, filename or directory
   * @return  peer.http.HttpResponse response object
   * @see     rfc://2518
   */
  public function delete($uri) {    
    return $this->getConnection($uri)->delete();  
  }
  
  /**
   * Activate VersionControl on a file
   *
   * @param   string uri, filename or directory
   * @return  peer.http.HttpResponse response object
   * @see     rfc://2518
   */
  public function version($uri) {    
    return $this->getConnection($uri)->version();
  }
  
  /**
   * Do a Report Request
   *
   * @param   string uri, filename or directory
   * @return  peer.http.HttpResponse response object
   * @see     rfc://2518
   */
  public function report($uri) {    
    return $this->getConnection($uri)->report();
  }
}
