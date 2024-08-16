#!/bin/bash

# Path to the Doctrine Migrations binary
DOCTRINE_BIN="./vendor/bin/doctrine-migrations"

# Path to your configuration files
MIGRATIONS_CONFIG="./conf/migrations.json"
DOCTRINE_CONFIG="./conf/doctrine.php"

# Running migrations
$DOCTRINE_BIN --configuration="$MIGRATIONS_CONFIG" migrate
