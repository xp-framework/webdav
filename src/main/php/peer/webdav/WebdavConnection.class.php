<?php namespace peer\webdav;

use peer\http\HttpConnection;

define('WEBDAV_METHOD_PROPFIND',  'PROPFIND');
define('WEBDAV_METHOD_PROPPATCH', 'PROPPATCH');
define('WEBDAV_METHOD_MKCOL',     'MKCOL');
define('WEBDAV_METHOD_LOCK',      'LOCK');
define('WEBDAV_METHOD_UNLOCK',    'UNLOCK');
define('WEBDAV_METHOD_COPY',      'COPY');
define('WEBDAV_METHOD_MOVE',      'MOVE');
define('WEBDAV_METHOD_DELETE',    'DELETE');
define('WEBDAV_METHOD_REPORT',    'REPORT');
define('WEBDAV_METHOD_VERSION',   'VERSION-CONTROL');

/**
 * Webdav connection
 */
class WebdavConnection extends HttpConnection {

  /**
   * Perform a Propfind request
   *
   * @param   var arg default NULL
   * @param   array headers default array()
   * @return  peer.http.HttpResponse response object
   */
  public function propfind($arg= null, $headers= array()) {
    return $this->request(WEBDAV_METHOD_PROPFIND, $arg, $headers);
  } 
  
  /**
   * Perform a Proppatch request
   *
   * @param   var arg default NULL
   * @param   array headers default array()
   * @return  peer.http.HttpResponse response object
   */
  public function proppatch($arg= null, $headers= array()) {
    return $this->request(WEBDAV_METHOD_PROPPATCH, new RequestData($arg), $headers);
  } 
  
  /**
   * Perform a mkCol request
   *
   * @param   var arg default NULL
   * @param   array headers default array()
   * @return  peer.http.HttpResponse response object
   */
  public function mkcol($arg= null, $headers= array()) {
    return $this->request(WEBDAV_METHOD_MKCOL, $arg, $headers);
  }  

  /**
   * Perform a lock request
   *
   * @param   var arg default NULL
   * @param   array headers default array()
   * @return  peer.http.HttpResponse response object
   */
  public function lock($arg= null, $headers= array()) {
    return $this->request(WEBDAV_METHOD_LOCK, $arg, $headers);
  } 
  
  /**
   * Perform a unlock request
   *
   * @param   var arg default NULL
   * @param   array headers default array()
   * @return  peer.http.HttpResponse response object
   */
  public function unlock($arg= null, $headers= array()) {
    return $this->request(WEBDAV_METHOD_UNLOCK, $arg, $headers);
  }
  
  /**
   * Perform a copy request
   *
   * @param   var arg default NULL
   * @param   array headers default array()
   * @return  peer.http.HttpResponse response object
   */
  public function copy($arg= null, $headers= array()) {
    return $this->request(WEBDAV_METHOD_COPY, $arg, $headers);
  }   
  
  /**
   * Perform a move request
   *
   * @param   var arg default NULL
   * @param   array headers default array()
   * @return  peer.http.HttpResponse response object
   */
  public function move($arg= null, $headers= array()) {
    return $this->request(WEBDAV_METHOD_MOVE, $arg, $headers);
  }
  
  /**
   * Perform a delete request
   *
   * @param   var arg default NULL
   * @param   array headers default array()
   * @return  peer.http.HttpResponse response object
   */
  public function delete($arg= null, $headers= array()) {
    return $this->request(WEBDAV_METHOD_DELETE, $arg, $headers);
  }
  
  /**
   * Perform a VersionControl request
   *
   * @param   var arg default NULL
   * @param   array headers default array()
   * @return  peer.http.HttpResponse response object
   */
  public function version($arg= null, $headers= array()) {
    return $this->request(WEBDAV_METHOD_VERSION, $arg, $headers);
  }
  
  /**
   * Perform a report request
   *
   * @param   var arg default NULL
   * @param   array headers default array()
   * @return  peer.http.HttpResponse response object
   */
  public function report($arg= null, $headers= array()) {
    return $this->request(WEBDAV_METHOD_REPORT, $arg, $headers);
  }
}
