#!/bin/sh

cd "`dirname $0`"
while [ -f "/tmp/doit_now.sh" ]
do
    sleep 1
done

echo "`pwd`/camdriver.sh $@" >/tmp/doit.sh

rm -f /tmp/ididit.txt
mv /tmp/doit.sh /tmp/doit_now.sh

while [ ! -f "/tmp/ididit.txt" ]
do
    sleep 1 
done

cat /tmp/ididit.txt
