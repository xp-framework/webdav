WebDAV protocol support for the XP Framework
========================================================================
WebDAV stands for "Web-based Distributed Authoring and Versioning". It
is a set of extensions to the HTTP protocol which allows users to 
collaboratively edit and manage files on remote web servers.

Example:

```php
use peer\webdav\WebdavClient;
use util\cmd\Console;

$client= new WebdavClient('http://user:password@xp-framework.net/xp/doc');
$response= $client->get('class.xslt');
while ($r= $response->readData()) {
  Console::write($r);
}
```
