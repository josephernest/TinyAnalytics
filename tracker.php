<?php 
function summarize()
{
    $lastsummarize = realpath(dirname(__FILE__)) . '/.lastsummarize';    
    if (!file_exists($lastsummarize) || ((time() - file_get_contents($lastsummarize)) > 3600))
    {
        shell_exec(realpath(dirname(__FILE__)) . '/summarize.py >/dev/null 2>&1 &');
        return true;
    }
    else
        return false;
}

function record_visit($sitename)
{
    $logfile = realpath(dirname(__FILE__)) . '/logs/' . $sitename . '.log';
    $txt = date("Y-m-d") . "\t" . date("H:i:s") . "\t" . $_SERVER['REMOTE_ADDR'] . "\t" . $_SERVER['REQUEST_URI'] . "\t" . $_SERVER['HTTP_USER_AGENT'] . "\t" . $_SERVER['HTTP_REFERER'] . PHP_EOL;
    file_put_contents($logfile, $txt, FILE_APPEND);
    summarize();
}
?>