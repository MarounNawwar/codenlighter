@echo off

REM Set the path to the Doctrine Migrations binary
set DOCTRINE_BIN=vendor\bin\doctrine-migrations.bat

REM Set the path to your configuration files
set MIGRATIONS_CONFIG=conf\migrations.json

REM Run the migrations
%DOCTRINE_BIN% --configuration="%MIGRATIONS_CONFIG%" migrate
