<?php
// Get a list of sites
$logfiles = glob('logs/*.log', GLOB_BRACE);
// Iterate through each site's logs
foreach($logfiles as $logfile) {
  // Name our working files based on the main site log name
  $visitorsfile = substr($logfile,0,-4).'.visitors';
  $referersfile = substr($logfile,0,-4).'.referers';
  $tmpfile = $logfile.'.tmp';
  
  // Check we can open the log file, then process it 
  if (($log = fopen($logfile, "r")) !== FALSE) {

    // While we have rows available in the log file, do stuff
    while (($data = fgetcsv($log, 1000, "\t")) !== FALSE) {

      // Assign variables to the data
      $rowdate = date("Y-m-d", $data[0]);
      $ipadr = $data[1];
      $ua = $data[3];
      $referer = $data[4];

      // If the User Agent is from a bot, then skip it
      $bots = array('bot', 'crawl', 'slurp', 'spider', 'yandex', 'WordPress', 'AHC', 'jetmon');
      foreach($bots as $bot){
        if (strpos($ua, $bot) !== false) {
          continue;
        }
      }
      
      // We only count each IP once a day (Unique daily visitors)
      if ( in_array($ipadr,$ipTracker[$rowdate]) ) {
        continue; 
      }
      
      // We only get this far for unique, non-bot data, so let's start recording it
      $visitors[$rowdate]++;
      
      // If we have referer data, add it to the array (if it doesn't yet exist)
      if ( $referer != "" && !in_array($referer,$referers)) {
        $referers[] = $referer;
      }

      // Track the IP per date
      $ipTracker[$rowdate][] = $ipadr;
      
    }
    fclose($log);
    
    // Write the Visitors file
    $writefile = fopen($visitorsfile,"w");
    fwrite($writefile,json_encode($visitors));
    fclose($writefile);
    
    // Write the Referrers file
    $writefile = fopen($referersfile,"w");
    fwrite($writefile,json_encode($referers));
    fclose($writefile);
    
    // Clear our variables for the next site
    unset($visitors);
    unset($referers);
    unset($ipTracker);
  }
}
// Update the .lastsummarize
$writefile = fopen('.lastsummarize',"w");
fwrite($writefile,time());
fclose($writefile);
