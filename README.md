# My Drinks

#### Tests 

In order to execute full test suite make sure your are using vagrant. 
Following phing target will execute all tests. 

```
$ bin/phing tests
```

Tests structure:

 * tests/ - only integration and functional tests
 * features/ - acceptance and some critical functional tests
 * spec/ - unit tests 
 
> Why tests are outside src/ folder? 
> Thanks to this separation tests/ features/ specs/ folders can be excluded from build artifact. 
> This allows to deploy only code required by application to run which makes artifact smaller.


#### Search Enigne  

Before using search engine first you need to create ElasticSearch index (kind of schema).
Next you need to index supplies and recipes.

```
$ bin/symfony mydrinks:es:create:index
$ bin/symfony mydrinks:es:index:supplies
$ bin/symfony mydrinks:es:index:recipes
```

#### Application

In order to run application using buildin php server execute following command:

```
$ bin/symfony server:run --docroot=public/
```
