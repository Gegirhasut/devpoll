<?php

require_once __DIR__ . '/db.php';

$pollId = isset($_GET['poll_id']) ? (int)$_GET['poll_id'] : 0;
$voted  = isset($_GET['voted']) && $_GET['voted'] === '1';

if ($pollId <= 0) {
    header('Location: index.php');
    exit;
}

$db = get_db();

$poll = $db->prepare("SELECT * FROM polls WHERE id = :id");
$poll->execute([':id' => $pollId]);
$poll = $poll->fetch();

if (!$poll) {
    header('Location: index.php');
    exit;
}

$results = $db->prepare("
    SELECT o.id, o.label, COUNT(v.id) as vote_count
    FROM options o
    LEFT JOIN votes v ON v.option_id = o.id
    WHERE o.poll_id = :pid
    GROUP BY o.id
    ORDER BY vote_count DESC
");
$results->execute([':pid' => $pollId]);
$options = $results->fetchAll();

$total = array_sum(array_column($options, 'vote_count'));

?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Results — DevPoll</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <header>
        <h1>📊 DevPoll</h1>
        <p class="subtitle">Developer opinions, no BS</p>
    </header>

    <?php if ($voted): ?>
        <div class="alert alert-success">✅ Your vote was recorded!</div>
    <?php endif; ?>

    <div class="poll-card">
        <h2><?= htmlspecialchars($poll['question']) ?></h2>
        <span class="badge"><?= htmlspecialchars($poll['category']) ?></span>
        <p class="total-votes"><?= $total ?> vote<?= $total !== 1 ? 's' : '' ?> total</p>

        <div class="results">
            <?php foreach ($options as $option): ?>
		<?php $pct = round($option['vote_count'] / $total * 100); ?>
                <div class="result-row">
                    <div class="result-label"><?= htmlspecialchars($option['label']) ?></div>
                    <div class="result-bar-wrap">
                        <div class="result-bar" style="width: <?= $pct ?>%"></div>
                    </div>
                    <div class="result-meta"><?= $option['vote_count'] ?> (<?= $pct ?>%)</div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="actions">
        <a href="index.php" class="btn btn-secondary">← All polls</a>
    </div>
</div>
</body>
</html>
