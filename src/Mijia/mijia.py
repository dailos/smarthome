#!/usr/bin/env python3
import sys
import time
import calendar
import os.path
import bluetooth._bluetooth as bluez
import paho.mqtt.client as paho

from bluetooth_utils import (toggle_device, enable_le_scan,
                             parse_le_advertising_events,
                             disable_le_scan, raw_packet_to_str)

commandFile = "/home/pi/smarthome/src/commands.txt"
broker = "volumio.local"
port = 1883
mapping = {
    "A4:C1:38:44:E9:EB": "erik",
    "A4:C1:38:C7:07:6F": "livingroom",
    "A4:C1:38:BC:6B:C8": "office"
}
expireAt = calendar.timegm(time.gmtime()) + 120

try:
    client = paho.Client()
    client.connect(broker, port)
except:
    print("Cannot initialize mqtt client")
    raise

toggle_device(0, True)

try:
    sock = bluez.hci_open_dev(0)
except:
    print("Cannot open bluetooth device ")
    raise

enable_le_scan(sock, filter_duplicates=True)


def le_advertise_packet_handler(mac, adv_type, data, rssi):
    data_str = raw_packet_to_str(data)

    if os.path.isfile(commandFile) or (calendar.timegm(time.gmtime()) > expireAt):
        disable_le_scan(sock)
        sys.exit()

    if mapping.has_key(mac):
        temp = float(int(data_str[22:26], 16)) / 10.0
        hum = int(data_str[26:28], 16)
        batt = int(data_str[28:30], 16)
        msg = '{"temperature": ' + \
            str(temp) + ',"humidity":' + str(hum) + \
            ',"battery": ' + str(batt) + '}'
        topic = mapping[mac] + "/termometer/status"
        client.publish(topic, msg)


try:
    parse_le_advertising_events(sock,
                                handler=le_advertise_packet_handler,
                                debug=False)
except KeyboardInterrupt:
    disable_le_scan(sock)
