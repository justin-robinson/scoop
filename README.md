[![Build Status](https://travis-ci.org/justin-robinson/scoop.svg?branch=php56)](https://travis-ci.org/justin-robinson/scoop)
[![Coverage Status](https://coveralls.io/repos/github/justin-robinson/scoop/badge.svg?branch=php56)](https://coveralls.io/github/justin-robinson/scoop?branch=php56)
#Scoop
## Multi site php framework and orm for mysql

###### Note: still a work in progress.

###Features
* Built for speed and efficiency
* Mysqli based and caches prepared statements
* All classes are autoloaded based on the full namespace, so no messy autoload config files
* Only two classes per table, one for core functionality and another for you to add to
* Properly documented for all that modern ide helper magic
* One installation can manage and segregate multiple code bases and db connections
* All configs are in php ( configs/db.php is the only one you'll need to touch )
* DB file generation just works ( bin/scoop --action generate_db_model )
* You can override any class or config option on a global or per site basis\

[Wiki][1]

[1]: https://github.com/justin-robinson/scoop/wiki
