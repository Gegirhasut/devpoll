<?php

require_once __DIR__ . '/db.php';

$db = get_db();

$polls = [
    [
        'question' => 'What is your primary programming language in 2024?',
        'category' => 'languages',
        'options'  => ['PHP', 'Python', 'TypeScript', 'Go', 'Rust', 'Java'],
    ],
    [
        'question' => 'Which architecture do you prefer for backend services?',
        'category' => 'architecture',
        'options'  => ['Monolith', 'Microservices', 'Serverless', 'Modular Monolith'],
    ],
    [
        'question' => 'What is your preferred frontend approach?',
        'category' => 'frontend',
        'options'  => ['React', 'Vue', 'HTMX + server-side', 'Svelte', 'Plain JS'],
    ],
    [
        'question' => 'How do you handle background jobs?',
        'category' => 'infrastructure',
        'options'  => ['Database queue', 'Redis + workers', 'RabbitMQ / Kafka', 'Cron jobs', 'Serverless functions'],
    ],
    [
        'question' => 'Which testing approach do you follow most?',
        'category' => 'practices',
        'options'  => ['TDD (test first)', 'Write tests after', 'Only integration tests', 'Mostly manual QA', 'No tests (honest answer)'],
    ],
    [
        'question' => 'Which AI coding tool do you use daily?',
        'category' => 'ai-tools',
        'options'  => ['GitHub Copilot', 'Cursor', 'Claude Code', 'ChatGPT', 'None'],
    ],
];

$pollCheck = $db->query("SELECT COUNT(*) as cnt FROM polls")->fetch();
if ((int)$pollCheck['cnt'] > 0) {
    echo "Already seeded. Skipping.\n";
    exit(0);
}

$insertPoll   = $db->prepare("INSERT INTO polls (question, category) VALUES (:q, :c)");
$insertOption = $db->prepare("INSERT INTO options (poll_id, label) VALUES (:poll_id, :label)");

foreach ($polls as $poll) {
    $insertPoll->execute([':q' => $poll['question'], ':c' => $poll['category']]);
    $pollId = $db->lastInsertId();
    foreach ($poll['options'] as $label) {
        $insertOption->execute([':poll_id' => $pollId, ':label' => $label]);
    }
}

echo "Seeded " . count($polls) . " polls.\n";
