## Installation

### Step 1) Installing the ClamAV binary:

Installing the ClamAV binary is pretty easy:
- If you have aptitude installed, enter the following in your terminal: `sudo apt-get install clamav`, or `sudo apt-get intall clamav-daemon`.
- On OSX, you're best off using homebrew and typing: `brew install clamav`.

**NOTE 1)** I did not yet find the time to get a nice (packaged) way for installing the daemon on OSX; help me out will you?
**NOTE 2)** Take note of the path you end up installing the binary `clamscan` in, as you will need it when you begin using
the adapter. You can find the path to the binary by entering either `$ whereis clamscan` or `$ whereis clamdscan` (notice the 'd')
in your terminal, depending on which package you decided to install.


### Step 2) Installing the adapter:

Installing the adapter is just as easy and can be done through either Composer or as a Git submodule:


#### Method a) As a Composer dependency

Add the following to your ``composer.json`` (see http://getcomposer.org/)
```json
"require":  {
    "cleentfaar/tissue-clamav-adapter": "~0.1"
}
```

#### Method b) As a Git submodule

Run the following commands to bring in the library as a submodule.
```
git submodule add https://github.com/cleentfaar/tissue-clamav-adapter.git vendor/cleentfaar/tissue-clamav-adapter
```


### Step 3) Register the namespaces

If you installed the bundle by composer, the namespace will be registered automatically (jump to step 3).

Otherwise, add the following two namespace entries to the `registerNamespaces` call in your autoloader:
```php
<?php
// path/to/your/autoload.php
$loader->registerNamespaces(array(
    // ...
    'CL\\Tissue\\Adapter\\ClamAV\\' => __DIR__.'/../vendor/cleentfaar/tissue-clamav-adapter',
    // ...
));
```


## Step 4) Ready?

Check out the [usage documentation](usage.md)!
