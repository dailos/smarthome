#!/bin/bash
MAC=$1

bt=$(timeout 15 gatttool -b $MAC --char-write-req --handle='0x0038' --value="0100" --listen | grep "Notification handle" -m 1)
file="../data/termometer_$MAC.data"

if [ -z "$bt" ]
then
	echo "Error, Reading failed for $MAC"
else
	hexa=$(echo $bt | awk '{print $6 " " $7 " " $8 " " $9 " " $10}')
	temphexa=$(echo $bt | awk '{print $7$6}' | tr '[:lower:]' '[:upper:]')
	humhexa=$(echo $bt | awk '{print $8}' | tr '[:lower:]' '[:upper:]')
	batthexa=$(echo $bt | awk '{print $10$9}' | tr '[:lower:]' '[:upper:]')
	temperature100=$(echo "ibase=16; $temphexa" | bc)
	humidity=$(echo "ibase=16; $humhexa" | bc)
	battery1000=$(echo "ibase=16; $batthexa" | bc)

	if [ $temperature100 -gt 32767 ];
	then
		temperature100=$(($temperature100 - 65536))
	fi

	# Add missing leading zero if needed (sed): "-.05" -> "-0.05" and ".05" -> "0.05"
	temperature=$(echo "scale=2; $temperature100 / 100" | bc | sed 's:^\(-\?\)\.\(.*\)$:\10.\2:')
	battery=$(echo "scale=3; $battery1000 / 1000" | bc)
	echo '{"temperature": "$temperature", "humidity": "$humidity", "battery": "$battery"}' > $file
fi