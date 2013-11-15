BookScanning Machine project manager
====================================

This is a small web-based application to manager book scanning projects : 

- creating a project for each book
- naming the book, its author, editor, isbn etc.
- scanning using 2 canon CHDK-compliant camera 
- cropping and rotating using imagemagick and gd
- creating a PDF using imagemagick

everything is PHP and BASH based, no database, projects are simple folder. See DESCRIPTION

License: GPL v3+, 
(C) 2012-2013 Benjamin Sonntag <benjamin at sonntag dot fr>



Installation Instructions
=========================

on Debian GNU/Linux, or Ubuntu, install a webserver with php : 

 aptitude install libapache2-mod-php5 apache2-mpm-prefork gphoto2

deploy this source code into /var/www/ 

 cd /var/www
 git clone https://github.com/vincib/bookscanner-project-manager.git

change /etc/apache2/sites-enabled/000-default to point into  /var/www/www

 DocumentRoot /var/www/www

edit /var/www/www/config_sample.php and save it into config.php

if necessary, download and compile ptpcam, put a copy of it into /var/www/sh/

go to http://localhost/ or http://yourmachinename/ to see the project manager.

Note: you will need to launch identify.php and generate.php perdiodically: they generate the thumbnails and the image pdf of the book. (ideally launch them at boottime in a shell loop like  "while true ; do php identify.php ; sleep 30 ; done")


Misc Information
================

We use this program at the French Bookscanner at La Quadrature du Net's location. Our website is at http://www.bookscanner.fr/

We are using the model created by Dan Reetz, available and discussed at http://www.diybookscanner.org/

You can buy one to build yourself if you live in Europe at http://www.diybookscanner.eu/

