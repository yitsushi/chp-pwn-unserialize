<?php

if (array_key_exists('source', $_GET)) {
  header('Content-Type: text/plain; charset=utf-8');
  echo file_get_contents(__FILE__);
  die();
}

session_start();
header('Content-Type: text/plain; charset=utf-8');

class Logger {
  protected $initMessage;
  protected $logFile;
  protected $endMessage;

  public function __construct($logfile = null) {
    $this->initMessage = '--- Start Logging ---';
    $this->endMessage = '--- End of Logging ---';
    $this->logFile = sprintf('logs/%s.log', time());

    if ($logfile != null) {
      $this->logFile = $logfile;
    }

    $this->write($this->initMessage);
  }

  public function write($message) {
    $fd = fopen($this->logFile, "a+");
    fwrite($fd, $message . "\n");
    fclose($fd);
  }

  public function __destruct() {
    $fd = fopen($this->logFile, "a+");
    fwrite($fd, $this->endMessage . "\n");
    fclose($fd);
  }
}

function saveData($data) {
  setcookie('D', base64_encode(serialize($data)));
}

function loadData() {
  if (array_key_exists('D', $_COOKIE)) {
    return unserialize(base64_decode($_COOKIE['D']));
  }
  return [rand(),rand(),rand(),rand()];
}

$logger = new Logger();

$data = loadData();

foreach ($data as $i => $value) {
  if ($value % (rand() % 3 + 2) === 0 || rand() % 50 == 0) {
    $data[$i] = rand();
    $logger->write("number changed: {$value} -> {$data[$i]}, it's ok.");
  }
}

saveData($data);

echo 'Your goal is to read the content of the `.htflag` file.', "\n\n";
echo "You can check the source code: /index.php?source\n";

echo "Your numbers:\n";
foreach ($data as $item) {
  echo " - {$item}\n";
}
