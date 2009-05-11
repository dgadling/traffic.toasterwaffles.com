<?php
#header("Content-type: image/png");

include_once("./gradient.php");

$base="/home/httpd/traffic.toasterwaffles.com";
$gradientCount = 16;
$dir = $_GET['dir'];
$width = $_GET['width'];
$height = $_GET['height'];

# Routers are funny, they don't tend to agree with humans about "in" and "out"
if ($dir == "in")
    $key = "out";
else
    $key = "in";

$duration = $_GET['dur'];
$directions = array( 'out' => 'coming OUT of', 'in' => 'going IN to' );
$colors = array( 'in' => array('start' => '00cee6', 'end' => '003339'), 
                'out' => array('start' => 'a8ff60', 'end' => '2a3f18') );
$startColor = $colors[$dir]['start'];
#$gradient = Gradient($startColor, 'ffffff', $gradientCount);
#$gradient = Gradient($startColor, '000000', $gradientCount);
$gradient = Gradient($startColor, $colors[$dir]['end'], $gradientCount);

$outFile = getcwd()."/images/$dir-$duration.png";
$command = "/usr/bin/rrdtool graph $outFile "
         . " --start -$duration "
         . " -z"
         . " -w $width -h $height"
         . " -v 'bytes per second'"
         . " -t 'Data $directions[$dir] the house'"
         . " -c 'BACK#000000'"
         . " -c 'CANVAS#232323'"
         . " -c 'FONT#f2f2f2'"
         . " -c 'GRID#f2f2f2'"
         . " -c 'MGRID#f2f2f2'"
         . " -c 'AXIS#f2f2f2'"
         . " -c 'FRAME#f2f2f2'"
         . " -c 'ARROW#f2f2f2'"
         . " 'DEF:$key=$base/myrouter.rrd:{$key}put:AVERAGE'"
         . " 'GPRINT:$key:LAST:Last\: %5.2lf %sbytes/sec'"
         . " 'GPRINT:$key:MAX:Max\: %5.2lf %sbytes/sec'"
         . " 'GPRINT:$key:AVERAGE:Avg\: %5.2lf%sbytes'"
         . " 'COMMENT:\c'"
         . " 'AREA:$key#$startColor'";
         ;


for ($i = 1; $i < $gradientCount; $i++) {
    $mult = 1 - ($i / (float)$gradientCount);
    $command .= " 'CDEF:shading$i=$key,$mult,*'"
              . " 'AREA:shading$i#$gradient[$i]'";
}

$command .= " > /dev/null";
$rc = 0;

system($command, $rc);
passthru("cat $outFile");
?>
