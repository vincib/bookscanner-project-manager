#!/bin/sh

do 
if [ `gpio read 4` -eq 1 ]
then
    gpio write 5 1 
    # then we wait for a down :) 
    while [ `gpio read 4` -eq 1 ]
    do
	sleep 0.2
    done
    # then we shoot :) 
    wget 
fi
sleep 0.5
while true