<?php

include 'vendor/autoload.php';

use Recapi\Environment;
use DB\Connection;

Environment::load(require 'config/creds.php');
Environment::bind('db', Connection::make(Environment::get('database')));