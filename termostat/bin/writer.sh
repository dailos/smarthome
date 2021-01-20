#!/bin/bash
MAC=$1

status=$(./eq3.exp $MAC devjson)
file="../data/termostat_$MAC.json"

if [ -z "$status" ]
then
	echo '{"Error":"Reading failed"}'
else
	echo $status > $file
fi