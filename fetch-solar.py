#!/usr/bin/env python

import urllib
import httplib2
import datetime, time
import os, sys
from BeautifulSoup import BeautifulSoup
from optparse import OptionParser
import ConfigParser


parser = OptionParser()
parser.add_option("-v", "--verbose", dest="verbose",
        help="Be more verbose", action="store_true")
parser.add_option("-d", "--date", dest="date",
        help="Date to look for data", metavar="%Y-%m-%d")
parser.add_option("-t", "--time", dest="time",
        help="Time of last update", metavar="%H:%M:%S")
parser.add_option("-l", "--lastFile", dest="cookieFile",
        help="Cookie file with the last time fetched", metavar="<FILE>",
        default="~/.solar-last")
parser.add_option("-c", "--config", dest="config",
        help="ConfigParser style config file with user/pass/serial/rrd", metavar="<FILE>",
        default="~/.solarrc")

(options, args) = parser.parse_args()

config = ConfigParser.RawConfigParser()
config.read(os.path.expanduser(options.config))
username = config.get('main', 'username')
password = config.get('main', 'password')
serial = config.get('main', 'serial')
rrdFile = config.get('main', 'rrd')

target = datetime.datetime.now()

if options.cookieFile:
    try:
        dateBits = open(os.path.expanduser(options.cookieFile), 'r').readline().split(':')
        dateBits = map(lambda x: int(x), dateBits)
        target = target.replace(hour=dateBits[0], minute=dateBits[1], second=dateBits[2])
    except Exception:
        target = target.replace(hour=0, minute=0, second=0)
else:
    target = target.replace(hour=0, minute=0, second=0)

if options.date:
    dateBits = map(lambda x: int(x), options.date.split("-"))
    target = target.replace(year=dateBits[0], month=dateBits[1], day=dateBits[2])

if options.time:
    lastTime = time.strptime(options.time, "%H:%M:%S")
    target = target.replace(hour=lastTime.tm_hour, minute=lastTime.tm_min,
            second=lastTime.tm_sec)


http = httplib2.Http()
headers = {
           'Content-Type': 'application/x-www-form-urlencoded',
                 'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        'Accept-Language': 'en-us,en;q=0.5',
        'Accept-Encoding': 'gzip,deflate',
         'Accept-Charset': 'ISO-8859-1,utf-8;q=0.7,*;q=0.7',
             'Keep-Alive': '300',
             'Connection': 'keep-alive',
             'User-Agent': 'Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; rv:1.9.0.10) Gecko/2009042316 Firefox/3.0.10 (.NET CLR 3.5.30729)',
                   'Host': 'www.pvwatch.com',
        }

response, content = http.request('http://www.pvwatch.com/', 'GET', headers=headers)
headers['Cookie'] = (response['set-cookie'].split(';'))[0]

url = 'http://www.pvwatch.com'
body = {'username': username, 'password': password, 'submit': 'Log in'}

http.request(url, 'POST', headers=headers, body=urllib.urlencode(body))
http.request('http://www.pvwatch.com/?s=do', 'GET', headers=headers)

headers['Referer'] = 'http://www.pvwatch.com/?s=do'
url = 'http://www.pvwatch.com/pvwatch.php'
data = dict(date=target.strftime("%Y-%m-%d"),
        serial_number=serial,
        datatype="datacols",
        pac="on",
        submit="Submit")

response, content = http.request(url, 'POST', urllib.urlencode(data), headers=headers)

soup = BeautifulSoup(content).contents[0]
soup.contents.pop(0)

lastRecord = datetime.datetime.now()
updates = []
for row in soup.contents:
    recordDate = datetime.datetime(*(time.strptime(row.contents[1].contents[0], "%Y-%m-%d")[0:3]))
    recordTime = time.strptime(row.contents[2].contents[0], "%H:%M:%S")
    recordedOn = recordDate.replace(hour=recordTime.tm_hour,
            minute=recordTime.tm_min, second=recordTime.tm_sec)

    production = row.contents[3].contents[0]

    if recordedOn > target:
        lastRecord = recordedOn
        updates.append("%s:%s" % (recordedOn.strftime("%s"), production))

if options.verbose:
    print "Found %d data points to add" % len(updates)
    print updates

os.system("/usr/bin/rrdtool update %s %s" % (rrdFile, ' '.join(updates)))
cookie = open(os.path.expanduser(options.cookieFile), 'w')
print >> cookie,lastRecord.strftime("%H:%M:%S")
cookie.close()
