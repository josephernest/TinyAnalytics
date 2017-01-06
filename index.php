<?php
if (file_exists('config.php'))
    include 'config.php';
else    
    $PASSWORD = 'abcdef';     // change your password here or create a config.php file: <?php $PASSWORD = '...';

session_set_cookie_params(30 * 24 * 3600, dirname($_SERVER['SCRIPT_NAME']));   // remember me
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
    echo '<html><head><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>TinyAnalytics</title><link rel="icon" href="favicon.ico"></head><body><form action="." method="post"><input type="password" name="pass" value="" autofocus><input type="submit" value="Submit"></form></body></html>';
    exit();
}

shell_exec('./summarize.py >/dev/null 2>&1 &');  // todo: do this only if timestamp is older than 1 hour / todo: do the same in tracker.php
usleep(250000); // check if .py finished? message? something else?
// todo: add timestamp 
?>

<html>
<head>
<title>TinyAnalytics</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0"> 
<link rel="icon" href="favicon.ico">
<style type="text/css">
* { border: 0; outline: 0; padding: 0; margin: 0; font-family: sans-serif; }
html { position: relative; min-height: 100%; }
.chart { width: 51%; height: 160px; }
.referers { height: 190px; overflow-y: auto; width: 45%; float: right; font-size: 0.8em; white-space: nowrap; overflow-x: hidden; }
.referers a { text-decoration: none; }
.site { padding-bottom: 30px; margin-bottom: 30px; border-bottom: 1px solid #eee; }
.site h1 { margin-bottom: 0px; }
p.warning { margin-bottom: 10px; }
.code { background-color: #f4f4f4; font-family: monospace; padding: 3px; }
pre { margin-bottom: 10px; }
#content { padding: 10px 10px 60px 10px; }
#footer { margin-top: 80px; color: #333; font-size: 0.8em; position: absolute; bottom: 10px; left: 10px; }
#footer a { color: #333; }
@media (max-width: 600px) { .referers { display: none; } .chart { width: 100%; } }
</style>
<script src="https://www.gstatic.com/charts/loader.js"></script>
</head>
<body>
<div id="content">
<?php     
$sites = glob("./logs/*.visitors", GLOB_BRACE);

if (!is_writable(realpath(dirname(__FILE__)))) echo '<p class="warning">&#8226; TinyAnalytics currently can\'t write data. Please give the write permissions to TinyAnalytics with:</p><pre class="code">chown -R ' . exec('whoami') . ' ' . realpath(dirname(__FILE__)) . '</pre>';
if (!is_executable(realpath(dirname(__FILE__)) . '/summarize.py')) echo '<p class="warning">&#8226; TinyAnalytics currently can\'t process the data. Please give the executable permission to TinyAnalytics with:</p><pre class="code">chmod +x ' . realpath(dirname(__FILE__)) . '/summarize.py' . '</pre>';
if (count($sites) == 0) echo "<p class=\"warning\">&#8226; No analytics data yet. Add this tracking code in your website's main PHP file, and then visit it at least once.</p><pre class=\"code\">&lt;?php\ninclude '" . realpath(dirname(__FILE__)) . "/tracker.php';\nrecord_visit('mywebsite');\n?&gt;</pre>";

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
?>
</div>
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
