# symfony_csv_import
Allows you to load data from the csv file to the database.
To import a file, you need to put it in the directory app/csvFiles/ and run command

``` 
php app/console import:csv
```

By default, the script searches file import.csv

But you can change name:
```
php app/console import:csv --name=custom_name.csv
```

All available options:
```
--name - name of file
--mode=test - run script in "test" mode. In this mode script will perform  everything the normal import does, but not insert the data into the database.
--delimiter=; - specify delimiter 
```

To avoid specifying the options in the console every time, you can configure the configuration in config.yml file:

```
it_csv_import:
    delimiter: "|"
    name: "custom_name.csv"
 ```
    
Priority is given to console commands.

By default:
```
delimiter: ','
name: 'import.csv'
```

To run phpUnit tests need run command:
```
php bin/phpunit -c app
```