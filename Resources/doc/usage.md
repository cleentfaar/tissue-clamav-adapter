## Usage

Assuming you have installed the clam binary as described in the [Installation documentation](installation.md),
and having configured your autoloader either automatically (Composer) or manually, here's how you start scanning for viruses
using ClamAV's engine:

```php
$adapter = new ClamAVAdapter('/usr/bin/clamscan');
$result = $adapter->scan(['/path/to/scary/file']);

// do we have a virus?
$result->hasVirus(); // returns either true or false

// what was scanned?
$result->getFiles(); // returns all the files that were scanned during the operation, as an array of strings (absolute paths)

// what whas detected then?
$result->getDetections(); // returns an array of `Detection` instances if one or more viruses were detected
```

**NOTE:** If you have tried any of the other adapters, this approach should appear familiar to you, as they all follow the
same signature of having a call to `scan()` and returning an instance of `ScanResult`.
