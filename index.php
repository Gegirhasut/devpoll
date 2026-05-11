<?php

require_once __DIR__ . '/db.php';

$db = get_db();

$polls = $db->query("SELECT * FROM polls ORDER BY id ASC")->fetchAll();

$pollsWithOptions = [];
$stmt = $db->prepare("SELECT * FROM options WHERE poll_id = :pid ORDER BY id ASC");
foreach ($polls as $poll) {
    $stmt->execute([':pid' => $poll['id']]);
    $pollsWithOptions[] = array_merge($poll, ['options' => $stmt->fetchAll()]);
}

$error = isset($_GET['error']) ? $_GET['error'] : null;

?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>DevPoll — Developer Opinion Polls</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <header>
        <h1>📊 DevPoll</h1>
        <p class="subtitle">Developer opinions, no BS</p>
    </header>

    <?php if ($error === 'invalid'): ?>
        <div class="alert alert-error">❌ Invalid vote. Please try again.</div>
    <?php endif; ?>

    <?php if (empty($pollsWithOptions)): ?>
        <div class="empty-state">
            <p>No polls yet. Run <code>php seed.php</code> to add some.</p>
        </div>
    <?php else: ?>
        <?php foreach ($pollsWithOptions as $poll): ?>
            <div class="poll-card">
                <span class="badge"><?= htmlspecialchars($poll['category']) ?></span>
                <h2><?= htmlspecialchars($poll['question']) ?></h2>

                <form action="vote.php" method="POST">
                    <input type="hidden" name="poll_id" value="<?= $poll['id'] ?>">
                    <div class="options">
                        <?php foreach ($poll['options'] as $option): ?>
                            <label class="option-label">
                                <input type="radio" name="option_id" value="<?= $option['id'] ?>" required>
                                <span><?= htmlspecialchars($option['label']) ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Vote</button>
                        <a href="results.php?poll_id=<?= $poll['id'] ?>" class="btn btn-secondary">See results</a>
                    </div>
                </form>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
</body>
</html>
