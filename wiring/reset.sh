#!/bin/sh

gpio reset

gpio mode 4 in
gpio mode 4 down
gpio mode 5 out
gpio write 5 0 

