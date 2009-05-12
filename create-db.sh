#!/bin/bash

# most current (5 minutes)
# 6 samples * 5 minutes = 30 minutes
# 24 samples * 5 minutes = 2 hours
# 288 samples * 5 minutes = 1 day
# 2016 samples * 5 minutes = 1 week

if [[ ! -f myrouter.rrd ]] ; then
    rrdtool create myrouter.rrd         \
             DS:input:COUNTER:600:U:U   \
             DS:output:COUNTER:600:U:U  \
             RRA:AVERAGE:0.5:1:600      \
             RRA:AVERAGE:0.5:6:700      \
             RRA:AVERAGE:0.5:24:775     \
             RRA:AVERAGE:0.5:288:797    \
             RRA:AVERAGE:0.5:2016:114   \
             RRA:MAX:0.5:1:600          \
             RRA:MAX:0.5:6:700          \
             RRA:MAX:0.5:24:775         \
             RRA:MAX:0.5:288:797        \
             RRA:MAX:0.5:2016:114
fi

# most current (1 minutes)
# 30 samples * 1 minute = 30 minutes
# 120 samples * 1 minute = 2 hours
# 1440 samples * 1 minute = 1 day
# 10800 samples * 1 minute = 1 week
if [[ ! -f mysolarpanel.rrd ]] ; then
    rrdtool create mysolarpanel.rrd     \
             DS:pac:GAUGE:1100:U:U      \
             RRA:AVERAGE:0.5:1:1100     \
             RRA:AVERAGE:0.5:30:3500    \
             RRA:AVERAGE:0.5:120:3875   \
             RRA:AVERAGE:0.5:1440:3985  \
             RRA:AVERAGE:0.5:10800:570  \
             RRA:MAX:0.5:1:1100         \
             RRA:MAX:0.5:30:3500        \
             RRA:MAX:0.5:120:3875       \
             RRA:MAX:0.5:1440:3985      \
             RRA:MAX:0.5:10800:570
fi
