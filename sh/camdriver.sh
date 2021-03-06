#!/bin/bash

# Script shell triggered by the web page to launch any ptpcam action.
# ptpcam must be in the current folder
# cam.sh must contain the two LEFT / RIGHT camera USB location. 

ptpcam=/var/www/sh/ptpcam.pi

# Usage: camdriver.sh <action> <parameter>
# action can be : 

# search
#   search for 2 cams, and set cam.sh accordingly (left/right)
#   dependency: lsusb, gphoto2
#   action: write configuration into "cam.sh"

# zoom <zoom value (1-120)>
#   set the zoom value of the 2 camera
#   dependency: ptpcam, cam.sh

# shoot 
#   shoot the 2 cameras
#   dependency: ptpcam, cam.sh

# get <directory left> <directory right>
#   get all files from one camera to the specified directory, and delete them all.

if [ -r "cam.sh" ]
then
    source ./cam.sh
fi
MYFOLD="`dirname $0`"
cd "$MYFOLD"
MYFOLD="`pwd`"

case $1 in
    search) 
	ALL="`lsusb |grep "04a9:3243"| sed -e 's/^Bus \([0-9]*\) Device \([0-9]*\):.*$/\1 \2 /' | tr '\n' ' ' `"
	CAM1B="`echo $ALL | awk '{print $1}' `"
	CAM1D="`echo $ALL | awk '{print $2}' `"
	CAM2B="`echo $ALL | awk '{print $3}' `"
	CAM2D="`echo $ALL | awk '{print $4}' `"
	if [ -z "$CAM1B" -o -z "$CAM1D" ]
	then
	    echo "No camera found"
	    echo "Please connect and switch on both camera"
	    exit 2
	fi
	WHICH1=$(gphoto2 --port "usb:${CAM1B},${CAM1D}" --get-config /main/settings/ownername|egrep "(left|right)"|sed -e 's/.*\(left\|right\)/\1/g')
	if [ -z "$CAM2B" -o -z "$CAM2D" ]
	then
	    echo "Only ONE camera found, which is the $WHICH1 one"
	    echo "Please connect and switch on both camera"
	    exit 3
	fi
	WHICH2=$(gphoto2 --port "usb:${CAM2B},${CAM2D}" --get-config /main/settings/ownername|egrep -i "(left|right)"|sed -e 's/.*\(left\|right\)/\1/g')
	tmpfile=/tmp/cam_tmp.sh.$$
	echo "#!/bin/sh" >$tmpfile

	if [ "$WHICH1" = "right" ]
	then
	    echo "USB_RIGHT_BUS=$CAM1B" >>$tmpfile
	    echo "USB_RIGHT_DEV=$CAM1D" >>$tmpfile
	else
	    if [ "$WHICH1" = "left" ]
	    then
	    echo "USB_LEFT_BUS=$CAM1B" >>$tmpfile
	    echo "USB_LEFT_DEV=$CAM1D" >>$tmpfile		
	    else
		echo "Can't identify camera 1 at bus $CAM1B:$CAM1D, please check"
		exit 4
	    fi
	fi

	if [ "$WHICH2" = "right" ]
	then
	    echo "USB_RIGHT_BUS=$CAM2B" >>$tmpfile
	    echo "USB_RIGHT_DEV=$CAM2D" >>$tmpfile
	else
	    if [ "$WHICH2" = "left" ]
	    then
	    echo "USB_LEFT_BUS=$CAM2B" >>$tmpfile
	    echo "USB_LEFT_DEV=$CAM2D" >>$tmpfile		
	    else
		echo "Can't identify camera 2 at bus $CAM2B:$CAM2D, please check"
		exit 5
	    fi
	fi

	if [ "$WHICH1" = "$WHICH2" ]
	then
	    echo "Camera 1 and 2 have the same position ! which is $WHICH1, please redo the camera configuration"
	    exit 6
	fi
	mv "$tmpfile" "cam.sh"
	echo "cam.sh successfully created, both camera properly detected"
	exit 0 
	;;

    prepare)
	# TODO : get back both $? and check it's 0
	$ptpcam --bus=${USB_LEFT_BUS} --dev=${USB_LEFT_DEV} --chdk="mode 1"
	$ptpcam --bus=${USB_RIGHT_BUS} --dev=${USB_RIGHT_DEV} --chdk="mode 1"
	$ptpcam --bus=${USB_LEFT_BUS} --dev=${USB_LEFT_DEV} --chdk="luar require('lptpgui').prepare(40)"
	$ptpcam --bus=${USB_RIGHT_BUS} --dev=${USB_RIGHT_DEV} --chdk="luar require('lptpgui').prepare(40)"
	exit 0 
	;;

    zoom)
	zoom=$2
	if [ "$zoom" -gt 120 -o "$zoom" -lt 0 ]
	then
	    echo "Zoom must be between 0 and 120"
	    exit 7
	fi
	# TODO : get back both $? and check it's 0
	$ptpcam --bus=${USB_LEFT_BUS} --dev=${USB_LEFT_DEV} --chdk="luar require('lptpgui').prepare($zoom)"
	$ptpcam --bus=${USB_RIGHT_BUS} --dev=${USB_RIGHT_DEV} --chdk="luar require('lptpgui').prepare($zoom)"
#	while [ "`jobs -p`" ]
#	do
#	    sleep 1  # todo : timeout at 10 ?
#	done
# -- in case of error --
#       echo "Can't zoom, an error occurred while sending command to a camera"
#       exit 8
	exit 0 
	;;

    shoot)
	left="$2"
	right="$3"
	if [ -z "$left" -o -z "$right" ] 
	then
	    echo "Left and Right directory missing"
	    exit 10
	fi
	# TODO : get back both $? and check it's 0
	( cd "$left" && \
	    "$ptpcam" --bus=${USB_LEFT_BUS} --dev=${USB_LEFT_DEV} --chdk="luar require('lptpgui').shoot()"
	) & 
	P1="`jobs -p`"
	sleep 0.2
	( cd "$right" && \
	    "$ptpcam" --bus=${USB_RIGHT_BUS} --dev=${USB_RIGHT_DEV} --chdk="luar require('lptpgui').shoot()"
	) & 
	P2="`jobs -p`"
	while [ -d "/proc/$P1" -o -d "/proc/$P2" ]
	do
	    sleep 1  # todo : timeout at 10 ?
	done
# -- in case of error --
#       echo "Can't shoot, an error occurred while sending command to a camera"
#       exit 9
	exit 0
	;;
    shootget)
	left="$2"
	right="$3"
	if [ -z "$left" -o -z "$right" ] 
	then
	    echo "Left and Right directory missing"
	    exit 10
	fi
	# TODO : get back both $? and check it's 0
	( cd "$left" && \
	    "$ptpcam" --bus=${USB_LEFT_BUS} --dev=${USB_LEFT_DEV} --chdk="luar require('lptpgui').shoot()" && \
	    sleep 3 && \
	    "$ptpcam" --bus=${USB_LEFT_BUS} --dev=${USB_LEFT_DEV} -G --overwrite && \
	    "$ptpcam" --bus=${USB_LEFT_BUS} --dev=${USB_LEFT_DEV} -D
	) & 
	P1="`jobs -p`"
	sleep 0.2
	( cd "$right" && \
	    "$ptpcam" --bus=${USB_RIGHT_BUS} --dev=${USB_RIGHT_DEV} --chdk="luar require('lptpgui').shoot()" && \
	    sleep 3 && \
	    "$ptpcam" --bus=${USB_RIGHT_BUS} --dev=${USB_RIGHT_DEV} -G --overwrite && \
	    "$ptpcam" --bus=${USB_RIGHT_BUS} --dev=${USB_RIGHT_DEV} -D
	) & 
	P2="`jobs -p`"
	while [ -d "/proc/$P1" -o -d "/proc/$P2" ]
	do
	    sleep 1  # todo : timeout at 10 ?
	done
# -- in case of error --
#       echo "Can't shoot, an error occurred while sending command to a camera"
#       exit 9
	exit 0
	;;
    get)
	left="$2"
	right="$3"
	if [ -z "$left" -o -z "$right" ] 
	then
	    echo "Left and Right directory missing"
	    exit 10
	fi
	# TODO : get back both $? and check it's 0
	( cd "$left" && \
	    "$ptpcam" --bus=${USB_LEFT_BUS} --dev=${USB_LEFT_DEV} -G --overwrite && \
	    "$ptpcam" --bus=${USB_LEFT_BUS} --dev=${USB_LEFT_DEV} -D
	) & 
	P1="`jobs -p`"
	( cd "$right" && \
	    "$ptpcam" --bus=${USB_RIGHT_BUS} --dev=${USB_RIGHT_DEV} -G --overwrite && \
	    "$ptpcam" --bus=${USB_RIGHT_BUS} --dev=${USB_RIGHT_DEV} -D
	) & 
	P2="`jobs -p`"
	while [ -d "/proc/$P1" -o -d "/proc/$P2" ]
	do
	    sleep 1  # todo : timeout at ??
	done
	exit 0 
esac

echo "Usage: camdriver.sh search | zoom <value> | shoot "
