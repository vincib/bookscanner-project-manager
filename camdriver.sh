#!/bin/sh

# Script shell triggered by the web page to launch any ptpcam action.
# ptpcam must be in the current folder
# cam.sh must contain the two LEFT / RIGHT camera USB location. 

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


if [ -r "cam.sh" ]
then
    source ./cam.sh
fi

case $1 in
    search) 
	lsusb |grep Canon| sed -e 's/^Bus \([0-9]*\) Device \([0-9]*\):.*$/\1 \2 /' | tr '\n' ' ' | read CAM1B CAM1D CAM2B CAM2D
	if [ -z "$CAM1B" -o -z "$CAM1D" ]
	then
	    echo "No camera found"
	    echo "Please connect and switch on both camera"
	    exit 1
	fi
	WHICH1=$(gphoto2 --port "usb:${CAM1B},${CAM1D}" --get-config /main/settings/ownername|egrep "(LEFT|RIGHT)"|sed -e "s/.*\(LEFT|RIGHT\)/\1/g")
	if [ -z "$CAM2B" -o -z "$CAM2D" ]
	then
	    echo "Only ONE camera found, which is the $WHICH1 one"
	    echo "Please connect and switch on both camera"
	    exit 1
	fi
	WHICH2=$(gphoto2 --port "usb:${CAM2B},${CAM2D}" --get-config /main/settings/ownername|egrep "(LEFT|RIGHT)"|sed -e "s/.*\(LEFT|RIGHT\)/\1/g")
	tmpfile=/tmp/cam_tmp.sh.$$
	echo "#!/bin/sh" >$tmpfile

	if [ "$WHICH1" = "RIGHT" ]
	then
	    echo "USB_RIGHT_BUS=$CAM1B" >>$tmpfile
	    echo "USB_RIGHT_DEV=$CAM1D" >>$tmpfile
	else
	    if [ "$WHICH1" = "LEFT" ]
	    then
	    echo "USB_LEFT_BUS=$CAM1B" >>$tmpfile
	    echo "USB_LEFT_DEV=$CAM1D" >>$tmpfile		
	    else
		echo "Can't identify camera 1 at bus $CAM1B:$CAM1D, please check"
		exit 1
	    fi
	fi

	if [ "$WHICH2" = "RIGHT" ]
	then
	    echo "USB_RIGHT_BUS=$CAM2B" >>$tmpfile
	    echo "USB_RIGHT_DEV=$CAM2D" >>$tmpfile
	else
	    if [ "$WHICH2" = "LEFT" ]
	    then
	    echo "USB_LEFT_BUS=$CAM2B" >>$tmpfile
	    echo "USB_LEFT_DEV=$CAM2D" >>$tmpfile		
	    else
		echo "Can't identify camera 2 at bus $CAM2B:$CAM2D, please check"
		exit 1
	    fi
	fi

	if [ "$WHICH1" = "$WHICH2" ]
	then
	    echo "Camera 1 and 2 have the same position ! which is $WHICH1, please redo the camera configuration"
	    exit 1
	fi
	mv "$tmpfile" "cam.sh"
	echo "cam.sh successfully created, both camera properly detected"
	exit 0 
	;;

    zoom)
	zoom=$2
	if [ "$zoom" -gt 120 -o "$zoom" -lt 0 ]
	then
	    echo "Zoom must be between 0 and 120"
	    exit 1
	fi
	./ptpcam --bus=${USB_LEFT_BUS} --dev=${USB_LEFT_DEV} --chdk="lua set_zoom($zoom)" &
	./ptpcam --bus=${USB_LEFT_BUS} --dev=${USB_LEFT_DEV} --chdk="lua set_zoom($zoom)" &
	while [ "`jobs -p`" ]
	do
	    sleep 1  # todo : timeout at 10 ?
	done
	;;

    shoot)
	./ptpcam --bus=${USB_LEFT_BUS} --dev=${USB_LEFT_DEV} --chdk="lua press_full('shoot')" &
	./ptpcam --bus=${USB_LEFT_BUS} --dev=${USB_LEFT_DEV} --chdk="lua press_full('shoot')" &
	while [ "`jobs -p`" ]
	do
	    sleep 1  # todo : timeout at 10 ?
	done
	
	;;
esac