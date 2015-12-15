<?php

require_once('phpconf.php');
require_once('phpfunc.php');
require_once('phpsecurity.php');

session_cache_expire(0);
session_cache_limiter('private_no_expire');
session_start();


if (strcmp(@$_SERVER['HTTP_REFERER'], SITE_URL.'signup.php') != 0 || !isset($_SESSION['signup'])) {
  header('Location: '.SITE_URL.'signup.php');
  exit;
}

$tmp = $_SESSION['signup'];
unset($_SESSION['signup']);

$data = array(
  ':email' => $tmp['email'],
  ':password' => $tmp['password'],
  ':name' => $tmp['name'],
  ':gender' => (int)$tmp['gender'],
  ':birthday' => $tmp['birthyear'].'-'.$tmp['birthmonth'].'-'.$tmp['birthday']
);

// DB接続
$pdo = connectDB();

if (1) {
  $sql = <<<EOS
INSERT INTO Users (
  Email,
  Password,
  Name,
  Gender,
  Birthday,
  Created
)
VALUES (
  :email,
  :password,
  :name,
  :gender,
  :birthday,
  NOW()
)
EOS;
  try {
    $pdo->beginTransaction();
    $stmt = $pdo->prepare($sql);
    $stmt->execute($data);
    $_SESSION['userid'] = $pdo->lastInsertId();
    $pdo->commit();
  }
  catch (PDOException $e) {
    $pdo->rollBack();
    header('Location: '.SITE_URL.'signup.php');
    exit;
  }
}
// DB切断
$pdo = null;

// アンケートページへ遷移
header('Location: '.SITE_URL.'questionnaire.php');
exit;

?>
