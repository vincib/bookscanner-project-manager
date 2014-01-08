#!/bin/sh

while true 
do

while [ ! -f "/tmp/doit_now.sh" ]
do
    sleep 0.2
done

echo "launching `cat /tmp/doit_now.sh`"

chmod a+x /tmp/doit_now.sh
rm -f /tmp/ididit.txt

. /tmp/doit_now.sh >/tmp/imdoingit.txt 2>&1

echo "done"

chmod a+w /tmp/imdoingit.txt
chown benjamin /tmp/imdoingit.txt
mv -f /tmp/imdoingit.txt /tmp/ididit.txt

rm -f /tmp/doit_now.sh

done
