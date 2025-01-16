# MoneyMate
MoneyMate is a self-hosted personal finance manager. It uses the PHP FinTS/HBCI library (https://github.com/nemiah/phpFinTS) in order to fetch transactions.

# Motivation
Back in 2017 I managed my finances within a spreadsheet. Soon I started the development of my personal finance manager tool because at this time I could not find any solution which meets my expectations and also as a software developer I was interested in creating my own solution ;)

MoneyMate is the successor to PersonalFinance (https://github.com/interose/PersonalFinance). The goal was to upgrade to the latest Symfony version while also gaining hands-on experience with new technologies like Stimulus, Turbo, and Tailwind by applying them in a practical project.

A big thank you to the SymfonyCasts team, and especially to Ryan Weaver!

# Highcharts
This software uses the charting library from [highcharts](https://www.highcharts.com/). Thanks to the highcharts team, this is really a great library. Easy to use with great flexibility. Please be aware that if you want to use this software commercially, you have to purchase a license.

# Screenshots

Todo

# Getting Started
Copy the .env file to .env.local and configure your environment. In order to use the FinTs library you have to register your software. Please check [phpFinTS](https://github.com/nemiah/phpFinTS#getting-started) for further information.

MoneyMate requires PHP 8.2 or higher, symfony 7.1 and a database (MySQL, MariaDB, PostgreSQL)

Run the follwing commands to install the vendor files, prepare the database and start the local server

```
$ make install 
$ make prepare-db
$ make run
```