# About

This is a very basic PHP script for parsing a MySQL slow query log and returning some basic stats:

* Total number of queries
* Average query time
* Number of queries per day
* Average query time per day

# Usage

`php parse.php <filename>`

You can also add an optional second parameter `<user>` - this will filter the results so you only see stats for that 
MySQL user. For example, if you have back-ups running with `mysqldump` under a different user from your main app, you 
could choose to only see the results from the app by running:

`php parse.php queries.log app_user`