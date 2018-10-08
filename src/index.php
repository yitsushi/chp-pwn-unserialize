<?php

if (array_key_exists('source', $_GET)) {
  header('Content-Type: text/plain; charset=utf-8');
  echo file_get_contents(__FILE__);
  die();
}

function sort_form(string $field): string {
  return '<a href="/?order_by=' . $field . '&direction=desc">&darr;</a>' .
         '<a href="/?order_by=' . $field . '&direction=asc">&uarr;</a>';
}

session_start();
header('Content-Type: text/html; charset=utf-8');

# it's fake :P just a static array, no SQL Inject here
require_once('database.php');

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

  public function write(string $message) {
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

function saveData(array $data) {
  setcookie('order', base64_encode(serialize($data)));
}

function loadData(): array {
  if (array_key_exists('order', $_COOKIE)) {
    return unserialize(base64_decode($_COOKIE['order']));
  }
  $data = ['field' => 'page_views', 'direction' => 'desc'];
  saveData($data);
  return $data;
}

$logger = new Logger();

$data = loadData();

$articles = Database::ListArticles();

if (isset($_GET['order_by']) && isset($_GET['direction'])) {
  $field = $_GET['order_by'];
  $direction = $_GET['direction'];
  if (isset($articles[0][$field]) && in_array($direction, ['asc', 'desc'])) {
    $data['field'] = $field;
    $data['direction'] = $direction;
    saveData($data);
  }
}

usort($articles, function($a, $b) use ($data) {
  if ($data['direction'] === "asc") return $a[$data['field']] > $b[$data['field']];
  else return $a[$data['field']] < $b[$data['field']];
});

?><html>
<head>
    <title>Article list</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
<div class='container'>

  <h1 class="header center orange-text">Public Statistics</h1>

<div class="center">
  Your goal is to read the content of the <code>.htflag</code> file.
</div>
<div class="center">
  You can check the source code: <a href="/index.php?source">/index.php?source</a>
</div>

  <h2 class="header center orange-text">All published articles</h2>

<table>
<tr>
  <th>ID <?php echo sort_form('id') ?></th>
  <th>Title <?php echo sort_form('title') ?></th>
  <th>Author <?php echo sort_form('author') ?></th>
  <th>Page Views <?php echo sort_form('page_views') ?></th>
  <th>Comments <?php echo sort_form('comments') ?></th>
</tr>
<?php foreach ($articles as $article): ?>
  <tr>
    <td><?php echo $article['id'] ?></td>
    <td><?php echo $article['title'] ?></td>
    <td><?php echo $article['author'] ?></td>
    <td><?php echo $article['page_views'] ?></td>
    <td><?php echo $article['comments'] ?></td>
  </tr>
<?php endforeach; ?>
</table>

</div>
</body>
</html>
