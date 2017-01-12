<?php 
function record_visit($sitename)
{
    $logfile = realpath(dirname(__FILE__)) . '/logs/' . $sitename . '.log';
    $txt = time() . "\t" . $_SERVER['REMOTE_ADDR'] . "\t" . $_SERVER['REQUEST_URI'] . "\t" . $_SERVER['HTTP_USER_AGENT'] . "\t" . $_SERVER['HTTP_REFERER'] . PHP_EOL;
    file_put_contents($logfile, $txt, FILE_APPEND);
}
?>
