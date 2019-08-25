#!/bin/bash
clear

echo
echo ----- Functions tests -----
echo 
phpunit --bootstrap ./bootstrap.php funcFileSystemTest.php
phpunit --bootstrap ./bootstrap.php funcArrayTest.php
phpunit --bootstrap ./bootstrap.php funcCSVTest.php
phpunit --bootstrap ./bootstrap.php funcStringTest.php
phpunit --bootstrap ./bootstrap.php funcTextTest.php
phpunit --bootstrap ./bootstrap.php funcCodePageTest.php
phpunit --bootstrap ./bootstrap.php funcHashtagTest.php
