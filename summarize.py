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
    #useragentsfile = 'useragents.txt'

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

    #try:
    #    with open(useragentsfile, 'r') as infile:
    #        useragents = set(json.load(infile))
    #except:
    #    useragents = set()

    today = datetime.datetime.today().strftime('%Y-%m-%d')

    with open(logfile, 'rb') as inp, open(tmpfile, 'w') as out:
        writer = csv.writer(out, delimiter='\t')
        for row in csv.reader(inp, delimiter='\t'):
            rowdate = row[0]
            ip = row[2]
            ua = row[4]            
            #useragents.add(ua)            
            
            if any(excl in ua for excl in ['bot', 'crawl', 'slurp', 'spider', 'yandex']):                
                continue            

            if len(row) > 5:
                referer = row[5]
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

    #with open(useragentsfile, 'w') as outfile:
    #    json.dump(sorted(list(useragents)), outfile, indent=2)

    os.chmod(logfile, 0o666)
    os.chmod(visitorsfile, 0o666)
    os.chmod(referersfile, 0o666)
