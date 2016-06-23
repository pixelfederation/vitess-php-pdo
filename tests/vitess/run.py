#!/usr/bin/env python

"""This is a demo for V3 features.

The script will launch all the processes necessary to bring up
the demo. It will bring up an HTTP server on port 8000 by default,
which you can override. Once done, hitting <Enter> will terminate
all processes. Vitess will always be started on port 12345.
"""

import json
import os
import subprocess
import signal
import sys
import time

from google.protobuf import text_format

from vtproto import vttest_pb2

sp = None


def start_vitess():
    """This is the main start function."""

    topology = vttest_pb2.VTTestTopology()
    keyspace = topology.keyspaces.add(name='user')
    keyspace.shards.add(name='-80')
    keyspace.shards.add(name='80-')
    keyspace = topology.keyspaces.add(name='lookup')
    keyspace.shards.add(name='0')

    vttop = os.environ['VTTOP']
    args = [os.path.join(vttop, 'py/vttest/run_local_database.py'),
          '--port', '12345',
          '--proto_topo', text_format.MessageToString(topology,
                                                      as_one_line=True),
          '--web_dir', os.path.join(vttop, 'web/vtctld'),
          '--schema_dir', 'schema']
    sp = subprocess.Popen(args, stdin=subprocess.PIPE, stdout=subprocess.PIPE)

    # This load will make us wait for vitess to come up.
    json.loads(sp.stdout.readline())
    return sp


def stop_vitess():
    global sp
    sp.stdin.write('\n')
    sp.wait()


def signal_term_handler(signal, frame):
    if sp is not None:
        stop_vitess()
    sys.exit(0)


def main():
    global sp
    signal.signal(signal.SIGTERM, signal_term_handler)
    sp = start_vitess()
    print "VTCombo is running..."

    while True:
        time.sleep(5)

if __name__ == '__main__':
    main()
