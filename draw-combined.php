<?php
include_once("./gradient.php");

$base="/home/httpd/traffic.toasterwaffles.com";
$gradientCount = 16;
$duration = $_GET['dur'];
$width = $_GET['width'];
$height = $_GET['height'];

$colors = array( 'in' => '00cee6', 'out' => 'a8ff60' );

$inGradient = Gradient($colors['out'], '2a3f18', $gradientCount);
$outGradient = Gradient($colors['in'], '003339', $gradientCount);

$outFile = getcwd()."/images/combined-$duration.png";
$command = "/usr/bin/rrdtool graph $outFile "
         . " --start -$duration "
         . " -z"
         . " -w $width -h $height"
         . " -v 'bytes per second'"
         . " -t 'Data through the house'"
         . " -c 'BACK#000000'"
         . " -c 'CANVAS#232323'"
         . " -c 'FONT#f2f2f2'"
         . " -c 'GRID#f2f2f2'"
         . " -c 'MGRID#f2f2f2'"
         . " -c 'AXIS#f2f2f2'"
         . " -c 'FRAME#f2f2f2'"
         . " -c 'ARROW#f2f2f2'"
         . " 'DEF:in=$base/myrouter.rrd:input:AVERAGE'"
         . " 'DEF:out=$base/myrouter.rrd:output:AVERAGE'"
         . " 'CDEF:total=out,in,+' 'AREA:total#{$colors['out']}:out'"
         . " 'AREA:out#{$colors['in']}:in'"
         . " 'GPRINT:total:LAST:Last\: %5.2lf %sBps'"
         . " 'GPRINT:total:MAX:Max\: %5.2lf %sBps'"
         . " 'GPRINT:total:AVERAGE:Avg\: %5.2lf%sBps'"
         ;


for ($i = 1; $i < $gradientCount; $i++) {
    $mult = 1 - ($i / (float)$gradientCount);
    $command .= " 'CDEF:shading_in_$i=in,$mult,*,out,+'"
              . " 'AREA:shading_in_$i#$inGradient[$i]'";
}

for ($i = 1; $i < $gradientCount; $i++) {
    $mult = 1 - ($i / (float)$gradientCount);
    $command .= " 'CDEF:shading_out_$i=out,$mult,*'"
              . " 'AREA:shading_out_$i#$outGradient[$i]'";
}

$command .= " > /dev/null";
$rc = 0;

system($command, $rc);

if ($rc == 0 ) {
    header("Content-type: image/png");
    passthru("cat $outFile");
} else {
    echo "$command<br/><br/>";
    echo $rc;
}
?>
