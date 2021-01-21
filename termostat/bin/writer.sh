#!/bin/bash
MAC=$1

status=$(./termostat/bin/eq3.exp $MAC devjson)
file="./termostat/data/$MAC.json"

if [ -z "$status" ]
then
	echo "Error, Reading failed for $MAC"
else
	echo $status > $file
fi