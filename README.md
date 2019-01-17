# Tables in Semantic

This is a mediawiki extension, to allow insertion of Table in semantic data using VE.

It is not possible to insert wikitext table in semantic data, so this convert wikitext table into html tables.

## run tests :


* make sur you ave run composer update without the --no-dev- option.
* launch php extensions/TablesInSemantic/tests/mw-phpunit-runner.php 

to run a single test (from mediawiki root dir) : 
  tests/phpunit/phpunit.php extensions/TablesInSemantic/tests/phpunit/TableParserTest.php