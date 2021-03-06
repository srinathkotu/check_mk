<?php
# +------------------------------------------------------------------+
# |             ____ _               _        __  __ _  __           |
# |            / ___| |__   ___  ___| | __   |  \/  | |/ /           |
# |           | |   | '_ \ / _ \/ __| |/ /   | |\/| | ' /            |
# |           | |___| | | |  __/ (__|   <    | |  | | . \            |
# |            \____|_| |_|\___|\___|_|\_\___|_|  |_|_|\_\           |
# |                                                                  |
# | Copyright Mathias Kettner 2014             mk@mathias-kettner.de |
# +------------------------------------------------------------------+
#
# This file is part of Check_MK.
# The official homepage is at http://mathias-kettner.de/check_mk.
#
# check_mk is free software;  you can redistribute it and/or modify it
# under the  terms of the  GNU General Public License  as published by
# the Free Software Foundation in version 2.  check_mk is  distributed
# in the hope that it will be useful, but WITHOUT ANY WARRANTY;  with-
# out even the implied warranty of  MERCHANTABILITY  or  FITNESS FOR A
# PARTICULAR PURPOSE. See the  GNU General Public License for more de-
# tails. You should have  received  a copy of the  GNU  General Public
# License along with GNU Make; see the file  COPYING.  If  not,  write
# to the Free Software Foundation, Inc., 51 Franklin St,  Fifth Floor,
# Boston, MA 02110-1301 USA.

$i=0;

$RRD = array();
foreach ($NAME as $i => $n) {
    $RRD[$n] = "$RRDFILE[$i]:$DS[$i]:MAX";
    $WARN[$n] = $WARN[$i];
    $CRIT[$n] = $CRIT[$i];
    $MIN[$n]  = $MIN[$i];
    $MAX[$n]  = $MAX[$i];
    $ACT[$n]  = $ACT[$i];
}

#
# First graph with all data
#
$ds_name[$i] = "Connections";
$def[$i]  = "";
$opt[$i]  = " --vertical-label 'Connections' --title '$hostname: $servicedesc' -l 0";

$def[$i] .= "DEF:active=${RRD['active']} ";
$def[$i] .= "GPRINT:active:LAST:\"  Active   Last %5.0lf\" ";
$def[$i] .= "GPRINT:active:MAX:\"Max %5.0lf\" ";
$def[$i] .= "GPRINT:active:AVERAGE:\"Average %5.1lf\" ";
$def[$i] .= "COMMENT:\"\\n\" ";

foreach ($this->DS as $KEY=>$VAL) {
    if (preg_match('/^(reading|writing|waiting)$/', $VAL['NAME'])) {
        $def[$i] .= "DEF:var${KEY}=${VAL['RRDFILE']}:${DS[$VAL['DS']]}:AVERAGE ";
        $def[$i] .= "AREA:var${KEY}".rrd::color($KEY).":\"".$VAL['NAME']."\":STACK ";
        $def[$i] .= "GPRINT:var${KEY}:LAST:\"Last %5.0lf\" ";
        $def[$i] .= "GPRINT:var${KEY}:MAX:\"Max %5.0lf\" ";
        $def[$i] .= "GPRINT:var${KEY}:AVERAGE:\"Average %5.1lf\" ";
        $def[$i] .= "COMMENT:\"\\n\" ";
   }
}

#
# Requests per Second
#
$i++;
$def[$i]     = "";
$opt[$i]     = " --title '$hostname: $servicedesc Requests/sec' -l 0";
$ds_name[$i] = "Requests/sec";
$color = '#000000';
foreach ($this->DS as $KEY=>$VAL) {
    if($VAL['NAME'] == 'requests_per_sec') {
        $def[$i]    .= rrd::def     ("var".$KEY, $VAL['RRDFILE'], $VAL['DS'], "AVERAGE");
        $def[$i]    .= rrd::line1   ("var".$KEY, $color, rrd::cut($VAL['NAME'],16), 'STACK' );
        $def[$i]    .= rrd::gprint  ("var".$KEY, array("LAST","MAX","AVERAGE"), "%6.1lf/s");
    }
}

#
# Requests per Connection
#
$i++;
$def[$i]     = "";
$opt[$i]     = " --title '$hostname: $servicedesc Requests/Connection' -l 0";
$ds_name[$i] = "Requests/Connection";
$color = '#000000';
foreach ($this->DS as $KEY=>$VAL) {
    if($VAL['NAME'] == 'requests_per_conn') {
        $def[$i]    .= rrd::def     ("var".$KEY, $VAL['RRDFILE'], $VAL['DS'], "AVERAGE");
        $def[$i]    .= rrd::line1   ("var".$KEY, $color, rrd::cut($VAL['NAME'],16), 'STACK' );
        $def[$i]    .= rrd::gprint  ("var".$KEY, array("LAST","MAX","AVERAGE"), "%6.1lf/s");
    }
}

#
# Connections per Second
#
$i++;
$def[$i]     = "";
$opt[$i]     = " --title '$hostname: $servicedesc Connections/sec' -l 0";
$ds_name[$i] = "Accepted/sec";
$color = '#000000';
foreach ($this->DS as $KEY=>$VAL) {
    if($VAL['NAME'] == 'accepted_per_sec') {
        $def[$i]    .= rrd::def     ("var".$KEY, $VAL['RRDFILE'], $VAL['DS'], "AVERAGE");
        $def[$i]    .= rrd::line1   ("var".$KEY, $color, rrd::cut($VAL['NAME'],16), 'STACK' );
        $def[$i]    .= rrd::gprint  ("var".$KEY, array("LAST","MAX","AVERAGE"), "%6.1lf/s");
    }
}
?>
