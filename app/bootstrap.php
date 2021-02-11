<?php
  require_once 'config/development.php';
  require_once 'helpers/session.php';
  require_once 'helpers/url.php';

  spl_autoload_register(function ($className) {
      require_once 'libraries/'. $className . '.php';
  });

  require __DIR__ . '/vendor/autoload.php';