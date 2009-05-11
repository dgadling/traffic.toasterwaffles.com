<?php
# Shamelessly copied from
# http://www.codingforums.com/showpost.php?s=d92d891da479cb5830851c56a94cd48d&p=755048&postcount=4
function Gradient($HexFrom, $HexTo, $ColorSteps)
{
    $FromRGB['r'] = hexdec(substr($HexFrom, 0, 2));
    $FromRGB['g'] = hexdec(substr($HexFrom, 2, 2));
    $FromRGB['b'] = hexdec(substr($HexFrom, 4, 2));

    $ToRGB['r'] = hexdec(substr($HexTo, 0, 2));
    $ToRGB['g'] = hexdec(substr($HexTo, 2, 2));
    $ToRGB['b'] = hexdec(substr($HexTo, 4, 2));

    $StepRGB['r'] = ($FromRGB['r'] - $ToRGB['r']) / ($ColorSteps);
    $StepRGB['g'] = ($FromRGB['g'] - $ToRGB['g']) / ($ColorSteps);
    $StepRGB['b'] = ($FromRGB['b'] - $ToRGB['b']) / ($ColorSteps);

    $GradientColors = array();

    for($i = 0; $i <= $ColorSteps; $i++)
    {
        $RGB['r'] = floor($FromRGB['r'] - ($StepRGB['r'] * $i));
        $RGB['g'] = floor($FromRGB['g'] - ($StepRGB['g'] * $i));
        $RGB['b'] = floor($FromRGB['b'] - ($StepRGB['b'] * $i));

        $HexRGB['r'] = sprintf('%02x', ($RGB['r']));
        $HexRGB['g'] = sprintf('%02x', ($RGB['g']));
        $HexRGB['b'] = sprintf('%02x', ($RGB['b']));

        $GradientColors[] = implode(NULL, $HexRGB);
    }

    return $GradientColors;
} 
?>
