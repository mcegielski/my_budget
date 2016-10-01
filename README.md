# My budget application

That application will consist of multiple components.
* backend/ - written in php backend part of the home budget application. Initially based on [https://github.com/tuupola/slim-api-skeleton](https://github.com/tuupola/slim-api-skeleton) with a lot to do still needed, but should be basically working already.
* frontend/ - to-be-done written in angularJS 2 frontend. That is the main concern, as current idea is to get familar with angular.
* mojegrosze-exporter/ - written in java application that exports transactions from home budget web application mojegrosze.pl to csv file
* csv-to-db/ - written in java application that copy data from the provided csv file to the db using given access details
* misc/ - general files, like db structure
