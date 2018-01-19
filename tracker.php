<?php 
function summarize($force = false)
{
    $lastsummarize = realpath(dirname(__FILE__)) . '/.lastsummarize';    
    if (!file_exists($lastsummarize) || ((time() - file_get_contents($lastsummarize)) > 3600) || $force)
    {
        shell_exec(realpath(dirname(__FILE__)) . '/summarize.php >/dev/null 2>&1 &');
        return true;
    }
    else
        return false;
}

function record_visit($sitename)
{
    $logfile = realpath(dirname(__FILE__)) . '/logs/' . $sitename . '.log';
    $txt = time() . "\t" . $_SERVER['REMOTE_ADDR'] . "\t" . $_SERVER['REQUEST_URI'] . "\t" . (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '') . "\t" . (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '') . PHP_EOL;
    file_put_contents($logfile, $txt, FILE_APPEND);
    summarize();
}
?>
