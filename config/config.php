<?php

// Configuramos la cookie de sesión para que dure: (60 seg * 60 min * 24 horas)
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params(60 * 60 * 24);
    session_start();
}

  $database = $_SESSION['database'] ?? getenv('DB_NAME') ?: 'prueba';
  $host = getenv('DB_HOST') ?: '127.0.0.1';
  $port = getenv('DB_PORT') ?: '3306';
  $username = getenv('DB_USER') ?: 'root';
  $password = getenv('DB_PASSWORD') ?: '';

$databaseAnterior = getenv('DB_LEGACY_NAME') ?: 'baseAntigua';

$config = [

    "database" => [

      "driver"     => "mysql",
      "host"     => $host,
      "port"     => $port,
      "dbname"   => $database,
      "username" => $username,
      "password" => $password
    ],
    "mailer" => [

      "smtp_debug"      => false,
      "host"            => "smtp.gmail.com",
      "smtp_auth"       => true,
      "username"        => "correo@gmail.com",
      "password"        => "123456",
      "smtp_secure"     => "ssl",
      "port"            => 465,
      "reply_to_email"  => "correo@gmail.com",
      "reply_to_name"   => "Uptaeb",
      "from_email"      => "correo@gmail.com",
      "from_name"       => "Uptaeb"
    ],
];

$configAnterior = [

    "database" => [

      "driver"     => "mysql",
      "host"     => "sql313.byethost3.com",
      "port"     => "3306",
      "dbname"   => $databaseAnterior,
      "username" => "b3_42308656",
      "password" => "venom12.."
    ],
    "mailer" => [

      "smtp_debug"      => false,
      "host"            => "smtp.gmail.com",
      "smtp_auth"       => true,
      "username"        => "correo@gmail.com",
      "password"        => "123456",
      "smtp_secure"     => "ssl",
      "port"            => 465,
      "reply_to_email"  => "correo@gmail.com",
      "reply_to_name"   => "Uptaeb",
      "from_email"      => "correo@gmail.com",
      "from_name"       => "Uptaeb"
    ],
];