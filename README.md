# php-slim-utilities
Functions for Slim 3 applications

## Install via composer

Not yet available on packagist.org. Waiting stable version.
 
Add in composer.json :
````json
{
    /* composer.json content */

    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/fjourneau/php-slim-utilities.git"
        }
    ],
    "require": {
        "fjourneau/slim-utilities": "dev-master"
    }
}   
````

If not mentionned, add :
````json
{
    "minimum-stability": "dev"
}
````


Then run ``composer install`` or ``composer update``.

## Classes available

````php
use fjourneau\SlimUtilities\FjoSlimContainerUtilities; 
use fjourneau\SlimUtilities\EloquentLightPaginator; 
use fjourneau\SlimUtilities\SlimTestCase; 
````

