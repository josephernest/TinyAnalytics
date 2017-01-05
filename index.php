<?php
$PASSWORD = 'abcdef';     // change your password here

session_start();
if ($_GET['action'] === 'logout')
{
    $_SESSION['logged'] = 0;
    header('Location: .');   // reload page to prevent ?action=logout to stay
}
if ($_POST['pass'] === $PASSWORD)
{
    $_SESSION['logged'] = 1;
    header('Location: .');   // reload page to prevent form resubmission popup when refreshing / this works even if no .htaccess RewriteRule 
}
if (!isset($_SESSION['logged']) || !$_SESSION['logged'] == 1)
{
    echo '<form action="." method="post"><input type="password" name="pass" value=""><input type="submit" value="Submit"></form>';
    exit();
}
?>

<html>
<head>
<title></title>
<style type="text/css">
* { padding: 0; margin: 0; outline: 0; font-family: sans-serif; }
html { padding: 10px; }
.chart { width: 48%; height: 160px; }
.referers { height: 190px; overflow-y: auto; width: 48%; float: right; font-size: 0.8em; }
.referers a { text-decoration: none; }
.site { padding-bottom: 30px; margin-bottom: 30px; border-bottom: 1px solid #eee; }
.site h1 { margin-bottom: 0px; }
.code { background-color: #f4f4f4; font-family: monospace; padding: 3px; }
pre { margin-bottom: 10px; }
#footer { margin-top: 80px; color: #333; font-size: 0.8em; }
#footer a { color: #333; }
@media (max-width: 600px) { .referers { display: none; } .chart { width: 100%; } }
</style>
<script src="https://www.gstatic.com/charts/loader.js"></script>
</head>
<body>
<?php     
$sites = glob("./logs/*.visitors", GLOB_BRACE);
foreach ($sites as $site)
{
    $sitename = basename($site, '.visitors');
    $referersfile = substr($site, 0, -9) . '.referers';
    $referers = '';
    $str = file_get_contents($referersfile);
    $urls = json_decode($str, true);
    foreach ($urls as $url)
    {
        $url = preg_replace('/[\"<>]+/', '', $url); 
        $displayedurl = preg_replace('#^https?://#', '', $url);
        $displayedurl = $url;
        $referers .= '<p><a href="' . $url . '">' . $displayedurl . '</a></p>' . PHP_EOL;
    }
    $str = file_get_contents($site);
    $visitors = json_decode($str, true);
    $points = '';
    $max = 0;
    for ($i = 0; $i < 30; $i++) 
    {
        $key = date("Y-m-d", time() - $i * 3600 * 24);
        $y = ((isset($visitors[$key])) ? $visitors[$key] : 0);
        $points .= $y . ',';
        $max = max($max, $y);
    }

    echo '<div class="site">' . PHP_EOL
        . '<div class="referers">' . PHP_EOL . $referers . '</div>' . PHP_EOL    
        . '<h1>' . $sitename . '</h1>' . PHP_EOL
        . '<div class="chart" data="' . substr($points, 0, -1) . '"></div>' . PHP_EOL
        . '</div>' . PHP_EOL . PHP_EOL;
}
if (count($sites) == 0) echo "<p>No analytics data yet. Add this tracking code in your website's main PHP file:</p><pre class=\"code\">&lt;?php\nrequire '" . realpath(dirname(__FILE__)) . "/tracker.php';\nrecord_visit('mywebsite');\n?&gt;</pre>Check the folder permissions as explained <a href=\"https://github.com/josephernest/TinyAnalytics#install\">here</a>, and run <span class=\"code\">./summarize.py</span>.";
?>

<div id="footer">Powered by <a href="https://github.com/josephernest/TinyAnalytics">TinyAnalytics</a>. <a href="?action=logout">Log out</a>.</div>

<script type="text/javascript">
google.charts.load('current', { callback: function () { drawChart(); window.addEventListener('resize', drawChart, false); },  packages:['corechart'] });

function drawChart() {
    var formatDate = new google.visualization.DateFormat({ pattern: 'MMM d' });
    var ticksAxisH;
    function createDataTable(values)
    {
        var dataTable = new google.visualization.DataTable();
        dataTable.addColumn('date', 'Day');
        dataTable.addColumn('number', 'Unique visitors');
        var today = new Date();
        ticksAxisH = [];
        for (var i = 0; i < values.length; i++) {
            var rowDate = new Date(today - i * 24 * 3600 * 1000);
            var xValue = { v: rowDate, f: formatDate.formatValue(rowDate) };
            var yValue = parseInt(values[i]);
            dataTable.addRow([xValue, yValue]);
            if ((i % 7) === 0) { ticksAxisH.push(xValue); }      // add tick every 7 days
        }
        return dataTable;
    }

    var charts = document.getElementsByClassName("chart");
    for(var i = 0; i < charts.length; i++)
    {
        var chart = new google.visualization.AreaChart(charts[i]);
        var data = charts[i].getAttribute('data').split(',');
        var dataTable = createDataTable(data);
        chart.draw(dataTable, {
            hAxis: { gridlines: { color: '#f5f5f5' }, ticks: ticksAxisH },
            legend: 'none',
            pointSize: 6,
            lineWidth: 3,
            colors: ['#058dc7'],
            areaOpacity: 0.1,
            vAxis: { gridlines: { color: '#f5f5f5' } },
        });  
    }
}
</script>
</body>
</html>
