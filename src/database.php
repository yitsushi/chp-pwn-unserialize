<?php

class Database {
  private static $articles;

  static function init(array $_articles): void {
    self::$articles = $_articles;
  }

  static function ListArticles(): array {
    return self::$articles;
  }
}

require_once('fake-db-content.php');
Database::init($_articles);
