# Sharing Economy Platform Builder

This is an open source solution based in Wordpress that enables users to create their own sharing economy platform just by configuring it and customizing it.

## Running the platform

* Requirements
    * Apache server
    * PHP 7
    * Mysql server

* Steps
    * Clone this repo in your web root
    * Import the database.sql file to Mysql
    * Visit the admin URL in your browser (ending in /wp-admin)
    * Login with the default credentials (user: admin pass: edupassword)
    * Go to Users > Your Profile > New Password, and change your password and save.
    
> git clone https://github.com/toxtli/sharing-economy-platform-builder.git

> mv sharing-economy-platform-builder gig

> cd gig

> mysql -u[user] -p

> mysql> create database gig;

> mysql> use gig;

> mysql> SET autocommit=0 ; source database.sql ; COMMIT ;
    
    
