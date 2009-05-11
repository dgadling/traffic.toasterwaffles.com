<?php
// These are the delta for height and width since my version of rrd graph
// doesn't have the "final size" option
$dw = 97;
$dh = 79;
$params = array("dur" => "3h",
                "width" => 2*(550 - $dw)+$dw,
                "height" => 309 - $dh,
            );

$graphs = array("Combined Traffic" => "combined",
                "Inbound Traffic" => "in",
                "Outbound Traffic" => "out",
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

            div#graph { width: <?php echo $params['width'] + $dw ?>px;
                       height: <?php echo $params['height'] + $dh ?>px; }
            div#container { 
                width: <?php echo $params['width'] + $dw ?>px;
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
            var graph = "combined";
            var height = <?php echo $params['height'] ?>;

            function updateHeader() {
                $("#header").text('Looking at '+dur+' of data');
            }

            function updateDur(newDur) {
                dur = newDur;
                updateHeader();
                $(loader(graph, timer));
                $('#graph_message').text('');
            }
            function updateGraph(newGraph) {
                graph = newGraph;
                $(loader(graph, timer));
                $('#graph_message').text('');
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
foreach ( $graphs as $desc => $g ) {
?>
                    <li><a href="#" onClick="updateGraph('<?php echo $g ?>');"><?php echo $desc; ?></a></li>
<?php
}
?>
                </ul>
                <ul>
<?php
foreach ( $intervals as $int ) {
?>
                    <li><a href="#" onClick="updateDur('<?php echo $int ?>');"><?php echo $int; ?></a></li>
<?php
}
?>
                </ul>
            </div>
            <div id="graph"></div>
            <div id="graph_message"></div>
        </div>
        <script type="text/javascript">
        var timer;
        var loader = function(graph, timer) {
            $('#graph').empty();
            $('#graph').addClass('loading');
            params = getParams(graph);
            var img = new Image();
            $(img)
                .load(function() {
                    $(this).hide();
                    $('#graph').removeClass('loading').append(this);
                    $(this).fadeIn();
<?php
if (substr_count(getcwd(), "dev") > 0) {
?>
                    $('#graph_message').text('updated with params = '+params);
<?php
} else {
?>
                    $('#graph_message').text('updated '+(new Date()).toLocaleString());
<?php
}
?>
                    timer = setTimeout("$(loader("+graph+", "+timer+"))", 1000*60*5);
                })

                .error(function() { $('#graph_message').text("Error updating: params = "+params); } )

                .attr('src', params);
        };


        function getParams(graph) {
            givens = "dur="+dur+"&width="+width+"&height="+height;
            switch(graph)
            {
                case "combined":
                    return "./draw-combined.php?"+givens;
                    break;
                case "in":
                case "out":
                    return "./draw-directions.php?dir="+graph+"&"+givens;
                    break;
                default:
                    return "./draw-"+graph+".php?"+givens;
                    break;
            }
        }
        $(loader(graph, timer));
        </script>
    </body>
</html>
