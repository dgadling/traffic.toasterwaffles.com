<?php
// These are the delta for height and width since my version of rrd graph
// doesn't have the "final size" option
$dw = 97;
$dh = 79;
$params = array("dur" => "3h",
                "width" => 550 - $dw,
                "height" => 309 - $dh,
            );

$paramList = array();
foreach($params as $k => $v)
{
    if(array_key_exists($k, $_GET)) {
        $params[$k] = $_GET[$k];
    }
    $paramList[] = "$k=$params[$k]";
}

$paramString = implode("&", $paramList);
$totalWidth = 2*($params['width'] + $dw);
$intervals = array("3h", "8h", "12h", "1d", "3d", "1week", "2weeks", "1month");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <link rel="icon" href="http://www.toasterwaffles.com/favicon.ico" type="image/x-icon" />
        <link rel="icon" href="http://www.toasterwaffles.com/favicon.gif" type="image/gif" />
        <title>Traffic stats</title>
        <style type="text/css">
            body {
                background: #232323;
                color: white;
                font-family: Verdana, sans-serif;
                font-size: x-small;
            }

            div.dirGraph {
                width: <?php echo $params['width'] + $dw ?>px;
                height: <?php echo $params['height'] + $dh ?>px;
            }
            div.combinedGraph {
                width: <?php echo $totalWidth ?>px;
                height: <?php echo $params['height'] + 93 ?>px;
            }

            div.in  { float: left; }
            div.out { float: right; }
            div.combined { text-align:center; }
            div#container { 
                width: <?php echo $totalWidth ?>px;
                margin: 0 auto;
            }
            div.loading { background: url(/spinner.gif) no-repeat center center; }
            h2 { text-align: center; }
            a:link,a:visited { color: #00cee6; }
            li {
                display: inline;
                list-style-type: none;
                padding: 0em .5em;
                text-align: center;
            }
            ul { text-align: center; }
            ul li:before { content: "| "; }
            ul li:first-child:before { content: ""; }
        </style>
        <script type="text/javascript" src="/jquery-1.3.2.min.js"></script>
        <script type="text/javascript" src="/jquery.throbber.min.js"></script>
        <script type="text/javascript">
            var dur = "<?php echo $params['dur'] ?>";
            var width = <?php echo $params['width'] ?>;
            var twiceWidth = <?php echo $dw+(2*$params['width']) ?>;
            var height = <?php echo $params['height'] ?>;
            function updateHeader() {
                $("#header").text('Looking at '+dur+' of data');
            }
            function updateDur(newDur) {
                dur = newDur;
                updateHeader();
                $(loader('in', './draw-directions.php', inTimer));
                $('#update_in').text('');
                $(loader('out', './draw-directions.php', outTimer));
                $('#update_out').text('');
                $(loader('combined', './draw-combined.php', combinedTimer));
                $('#update_combined').text('');
            }

        </script>
    </head>
    <body>
        <div id="container">
            <h2 id="header"></h2>
            <script type="text/javascript">updateHeader()</script>
            <div id="nav">
                <ul>
<?php
foreach ( $intervals as $int ) {
?>
                    <li><a href="#" onClick="updateDur('<?php echo $int ?>')"><?php echo $int; ?></a></li>
<?php
}
?>
                </ul>
            </div>
            <div class="dirGraph in" id="in"></div>
            <div class="dirGraph out" id="out"></div>
            <br clear="both" />
            <div class="in" id="update_in"></div>
            <div class="out" id="update_out"></div>
            <br clear="both" />
            <div class="combinedGraph combined" id="combined"></div>
            <div class="combined" id="update_combined"></div>
        </div>
        <script type="text/javascript">
        var inTimer, outTimer, combinedTimer;
        var loader = function(dir, baseURL, timer) {
            $('#'+dir).empty();
            $('#'+dir).addClass('loading');
            if (dir == 'combined') {
                params = "?dur="+dur+"&width="+twiceWidth+"&height="+height;
            } else {
                params = "?dir="+dir+"&dur="+dur+"&width="+width+"&height="+height;
            }
            var img = new Image();
            $(img)
                .load(function() {
                    $(this).hide();
                    $('#'+dir).removeClass('loading').append(this);
                    $(this).fadeIn();
                    $('#update_'+dir).text('updated '+(new Date()).toLocaleString());
                    timer = setTimeout("$(loader('"+dir+"', '"+baseURL+"', "+timer+"))", 1000*60*5);
                })

                .error(function() { alert("error loading image. div = "+dir+" ; params = "+params); })

                .attr('src', baseURL+params);
        };

        $(loader('in', './draw-directions.php', inTimer));
        $(loader('out', './draw-directions.php', outTimer));
        $(loader('combined', './draw-combined.php', combinedTimer));
        </script>
    </body>
</html>
