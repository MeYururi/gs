<?php
require_once('phpconf.php');
require_once('phpfunc.php');

session_cache_expire(0);
session_cache_limiter('private_no_expire');
session_start();


$planid = $_SESSION['planid'];

// DB接続
$pdo = connectDB();

$stmt = null;

$pdo = connectDB();

$rate = (int)($_POST['satisfy']);
$data = array(
  ':planid' => $planid,
  ':rating' => $rate,
  ':impression' => $_POST['impression']
);

if (1) {
$ins = <<<EOS
    INSERT INTO Feedbacks (
    PlanID,
    Rating,
    Impression,
    Created
  )
  VALUES (
    :planid,
    :rating,
    :impression,
    NOW()
  );
EOS;
try {
  $pdo->beginTransaction();
  $stmt = $pdo->prepare($ins);
  $stmt->execute($data);
  $pdo->commit();
  header('Location: '.SITE_URL.'timeline.php');
} catch (PDOException $e) {
    $pdo->rollBack();
    echo 'miss';
    exit;
  }
}

$pdo = null;
?>
