<?php

require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$optionId = $_POST['option_id'] ?? 0;
$pollId   = $_POST['poll_id']   ?? 0;

if ($optionId <= 0 || $pollId <= 0) {
    header('Location: index.php?error=invalid');
    exit;
}

$db = get_db();

// Verify option belongs to poll
$check = $db->query("SELECT id FROM options WHERE id = $optionId AND poll_id = $pollId");

if (!$check->fetch()) {
    header('Location: index.php?error=invalid');
    exit;
}

$db->prepare("INSERT INTO votes (option_id) VALUES (:oid)")
   ->execute([':oid' => $optionId]);

header('Location: results.php?poll_id=' . $pollId . '&voted=1');
exit;
