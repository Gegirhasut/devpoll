# DevPoll

Developer opinion polls. Plain PHP + SQLite. No framework, no build step.

## Setup

```bash
git clone <repo_url>
cd devpoll
php seed.php          # seed initial polls
php -S localhost:8000 # run dev server
```

Open http://localhost:8000

## Project structure

```
index.php     — poll list + voting forms
vote.php      — POST handler, redirects to results
results.php   — per-poll result page with bar chart
db.php        — PDO SQLite helper, schema init
seed.php      — seeds initial poll data
style.css     — dark theme
```

## PR scenario backlog (for QALens.AI testing)

| # | Branch | Description | Type |
|---|--------|-------------|------|
| 1 | `feat/new-poll-ai-tools` | Add "What AI coding tool do you use?" poll to seed | feature |
| 2 | `feat/vote-dedup-ip` | Prevent multiple votes per IP per poll | feature |
| 3 | `bug/sql-injection-vote` | Intentional: unsanitized `$_POST` in vote.php | security bug |
| 4 | `refactor/db-class` | Wrap db.php in a `Database` class | refactor |
| 5 | `feat/admin-reset` | Add `/admin.php` reset-votes page without auth | security bug |
| 6 | `feat/vote-expiry` | Close polls after 30 days | feature |
| 7 | `feat/share-button` | Copy result URL to clipboard button | UI |
| 8 | `test/vote-handler` | Add PHPUnit test for vote.php logic | tests |
| 9 | `fix/empty-poll-crash` | results.php crashes when poll has no votes (division by zero) | bugfix |
| 10 | `feat/category-filter` | Filter polls by category tab on index | feature |

## License

MIT
