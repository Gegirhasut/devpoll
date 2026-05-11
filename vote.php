<?php

require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$optionId = isset($_POST['option_id']) ? (int)$_POST['option_id'] : 0;
$pollId   = isset($_POST['poll_id'])   ? (int)$_POST['poll_id']   : 0;

if ($optionId <= 0 || $pollId <= 0) {
    header('Location: index.php?error=invalid');
    exit;
}

$db = get_db();

// Verify option belongs to poll
$check = $db->prepare("SELECT id FROM options WHERE id = :oid AND poll_id = :pid");
$check->execute([':oid' => $optionId, ':pid' => $pollId]);

if (!$check->fetch()) {
    header('Location: index.php?error=invalid');
    exit;
}

$db->prepare("INSERT INTO votes (option_id) VALUES (:oid)")
   ->execute([':oid' => $optionId]);

header('Location: results.php?poll_id=' . $pollId . '&voted=1');
exit;
