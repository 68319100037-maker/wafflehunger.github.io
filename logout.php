<?php
require_once __DIR__ . '/page_template.php';

$_SESSION = [];
session_destroy();

header('Location: index.php');
exit();
