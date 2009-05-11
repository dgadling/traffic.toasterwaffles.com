#!/bin/bash

# most current (5 minutes)
# 6 samples * 5 minutes = 30 minutes
# 24 samples * 5 minutes = 2 hours
# 288 samples * 5 minutes = 1 day
# 2016 samples * 5 minutes = 1 week

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
