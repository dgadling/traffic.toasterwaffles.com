#!/bin/bash

export PYTHONPATH=$PYTHONPATH:/home/httpd/traffic.toasterwaffles.com
vars="ifInOctets.4 ifOutOctets.4"

eval `snmpget -O Qs -v 1 -c public 192.168.1.1 $vars | sed -e 's/\.[0-9] = /=/'`

rrdtool update /home/dave/traffic.toasterwaffles.com/myrouter.rrd N:${ifInOctets}:${ifOutOctets}
rrdtool update /home/dave/traffic.toasterwaffles.com/myrouter.rrd.bak N:${ifInOctets}:${ifOutOctets}
/home/dave/traffic.toasterwaffles.com/fetch-solar.py
