#!/bin/bash
MAC=$1

status=$(./Scripts/eq3.exp $MAC devjson)
file="./Data/$MAC.json"

if [ -z "$status" ]
then
	echo "Error, Reading failed for $MAC"
else
	echo $status > $file
fi