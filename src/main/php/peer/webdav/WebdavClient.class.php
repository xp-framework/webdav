<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
  
  uses(
    'peer.http.RequestData',
    'peer.webdav.WebdavConnection',
    'peer.Header',
    'util.MimeType'
  );

  /**
   * WebDAV Client.
   * 
   * WebDAV stands for "Web-based Distributed Authoring and
   * Versioning". It is a set of extensions to the HTTP protocol
   * which allows users to collaboratively edit and manage files
   * on remote web servers.
   *
   * <code>
   *   uses(
   *     'peer.webdav.WebdavClient',
   *     'peer.URL'
   *   );
   *   
   *   $client= new WebdavClient(new URL('http://kiesel:password@xp-framework.net/xp/doc'));
   *   try {
   *     $response= $client->get('class.xslt');
   *     while ($r= $response->readData()) {
   *       Console::write($r);
   *     }
   *   } catch(XPException $e) {
   *     $e->printStackTrace();
   *     exit(-1);
   *   }
   * </code>
   *
   * @see       http://www.webdav.org
   * @see       rfc://2518
   * @see       rfc://3253
   * @see       rfc://3648
   * @purpose   Provide a client to access an webdav server
   */
  class WebdavClient extends Object {
    public 
      $url=     NULL,
      $uri=     '',
      $path=    '',
      $depth=   '1',
      $xml=     NULL,
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
    public function getConnection($uri= NULL) {
      return new WebdavConnection(
        new URL($this->url->getURL().$this->path.($uri === NULL ? '' : '/'.$uri))
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
    public function read($uri= NULL, $xml= NULL, $depth= '1') {     
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
    public function put($file, $uri= NULL) {  
      // If no uri or filename is specified, take the original filename  
      if ($uri === NULL) $uri= $file->getFilename();
            
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
    public function get($uri= NULL) {    
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
    public function proppatch($xml, $uri= NULL) {          
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
     * @param   bool overwrite, default FALSE
     * @param   var depth, default Infinity
     * @return  peer.http.HttpResponse response object
     * @see     rfc://2518
     */
    public function copy($source, $destination, $overwrite= FALSE, $depth= 'Infinity') {        
      return $this->getConnection($source)->copy(
        NULL,
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
     * @param   bool overwrite, default FALSE
     * @return  peer.http.HttpResponse response object
     * @see     rfc://2518
     */
    public function move($source, $destination, $overwrite= FALSE) {   
      return $this->getConnection($source)->move(
        NULL,
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
        NULL,
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
?>
