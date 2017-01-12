#!/usr/bin/python

import csv
import glob
import json
import os, sys
import datetime

os.chdir(sys.path[0])    # cd to the script dir, even if called from cron

for logfile in glob.glob('logs/*.log'):
    visitorsfile = logfile[:-4] + '.visitors'
    tmpfile = logfile + '.tmp'
    referersfile = logfile[:-4] + '.referers'

    S = dict()

    try:
        with open(visitorsfile, 'r') as infile:
            visitors = json.load(infile)
    except:
        visitors = dict()

    try:
        with open(referersfile, 'r') as infile:
            referers = set(json.load(infile))
    except:
        referers = set()

    today = datetime.datetime.today().strftime('%Y-%m-%d')

    with open(logfile, 'rb') as inp, open(tmpfile, 'w') as out:
        writer = csv.writer(out, delimiter='\t')
        for row in csv.reader(inp, delimiter='\t'):
            rowdate = datetime.datetime.fromtimestamp(int(row[0])).strftime('%Y-%m-%d')
            ip = row[1]
            ua = row[3]            
            
            if any(excl in ua for excl in ['bot', 'crawl', 'slurp', 'spider', 'yandex']):                
                continue            

            referer = row[4]
            if referer != "":
                referers.add(referer)

            if rowdate not in S:        
                S[rowdate] = set()      # let's use a set in order to count each IP only once a day

            S[rowdate].add(ip)
            if rowdate == today:
                writer.writerow(row)

    os.remove(logfile)
    os.rename(tmpfile, logfile)

    for k in S:
        visitors[str(k)] = str(len(S[k]))

    with open(visitorsfile, 'w') as outfile:
        json.dump(visitors, outfile)

    with open(referersfile, 'w') as outfile:
        json.dump(sorted(list(referers)), outfile)

    with open('.lastsummarize', 'w') as outfile:
        outfile.write(str(int(time.time())))

    os.chmod(logfile, 0o666)
    os.chmod(visitorsfile, 0o666)
    os.chmod(referersfile, 0o666)
