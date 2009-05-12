<?php
include_once("./gradient.php");

$base="/home/httpd/traffic.toasterwaffles.com";
$gradientCount = 16;
$width = $_GET['width'];
$height = $_GET['height'];
$duration = $_GET['dur'];

$startColor = 'ffffcc';
$endColor = '3f3f33';
$gradient = Gradient($startColor, $endColor, $gradientCount);

$outFile = getcwd()."/images/solar-$duration.png";
$command = "/usr/bin/rrdtool graph $outFile "
         . " --start -$duration "
         . " -z"
         . " -w $width -h $height"
         . " -v 'watts'"
         . " -t 'Power generated (watts)'"
         . " -c 'BACK#000000'"
         . " -c 'CANVAS#232323'"
         . " -c 'FONT#f2f2f2'"
         . " -c 'GRID#f2f2f2'"
         . " -c 'MGRID#f2f2f2'"
         . " -c 'AXIS#f2f2f2'"
         . " -c 'FRAME#f2f2f2'"
         . " -c 'ARROW#f2f2f2'"
         . " 'DEF:pac=$base/mysolarpanel.rrd:pac:AVERAGE'"
         . " 'GPRINT:pac:LAST:Last\: %5.2lf %sWpm'"
         . " 'GPRINT:pac:MAX:Max\: %5.2lf %sWpm'"
         . " 'GPRINT:pac:AVERAGE:Avg\: %5.2lf%sWpm'"
         . " 'COMMENT:\c'"
         . " 'AREA:pac#$startColor'";
         ;


for ($i = 1; $i < $gradientCount; $i++) {
    $mult = 1 - ($i / (float)$gradientCount);
    $command .= " 'CDEF:shading$i=pac,$mult,*'"
              . " 'AREA:shading$i#$gradient[$i]'";
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
