CakeBootstrap
=============

CakeBootstrap is a [CakePHP](http://cakephp.org) plugin that helps to output 
HTML markup in a format compatible with the 
[Twitter Bootstrap](https://github.com/twitter/bootstrap) toolkit.

CakeBootstrap classes extend their related CakePHP core classes and can be used 
as drop-in replacements with little change to existing code.  


Installation
------------

1. Place files in a `twitter_bootstrap` folder in your plugins directory. 
    (e.g. `app/plugins/twitter_bootstrap/`), 
    
2. Include bootstrap.css or bootstrap.min.css in your layout:

        $this->Html->css('/twitter_bootstrap/bootstrap/bootstrap.min');

3. Add the needed helpers within your controller:

	    class MyController extends AppController {
		    $helpers = array('TwitterBootstrap.BootstrapForm');
	    }
        
4. Use the CakeBootstrap classes to output your markup:

        echo $this->BootstrapForm->create('MyModel');
        echo $this->BootstrapForm->input('field');
        echo $this->BootstrapForm->submit();
        echo $this->BootstrapForm->end();

    
Versioning
----------

Distribution package versions are in the format

    {CakePHP version}-{Twitter Bootstrap version}-{CakeBootstrap Version}

The CakePHP and Twitter Bootstrap versions are major release version numbers 
(e.g. 1.3 for CakePHP and 1 for Twitter Bootstrap)

The CakeBootstrap portion follows 
[Semantic Versioning guidelines](http://semver.org/).


Bug tracker
-----------

Have a bug? Please create an issue on GitHub.

https://github.com/gapple/CakeBootstrap/issues


Author
------

**Geoff Appleby**

+ http://github.com/gapple
+ http://twitter.com/gappleca
+ http://gapple.ca
