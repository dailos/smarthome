#!/bin/bash
MAC=$1

status=$(./Scripts/eq3.exp $MAC devjson)
file="./Data/$MAC.json"

if [ "$status" ]
then
	echo $status > $file
fi