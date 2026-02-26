# PPvR

[PPvR](https://ppvr.andrus.io/) is an alternative osu! ranking system based on the scores of scoreposts on the game's subreddit [/r/osugame](https://www.reddit.com/r/osugame/).

PPvR parses the scoreposts of the subreddit and calculates a score for each player based on the scores (up- and downvotes) of the scoreposts related to them. A ranking for scorepost authors is also available. For more details, check the [FAQ](https://ppvr.andrus.io/faq).

## Requirements

- PHP 8.4+
- Composer
- Node.js & npm
- SQLite (development) or MySQL/PostgreSQL (production)

### for development:
- Docker (all the requirements above)

## Installation

```bash
# Clone the repository
git clone https://github.com/your-username/ppvr-web.git
cd ppvr-web

# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install

# Copy environment file and configure
cp .env.example .env
php artisan key:generate

# Run database migrations
php artisan migrate

# Build frontend assets
npm run dev       # Development
npm run prod      # Production
```

## Development

```bash
# Start the development server
./vendor/bin/sail build --no-cache
./vendor/bin/sail up -d

# Watch for frontend changes
npm run watch
```

## Artisan Commands

PPvR includes several custom Artisan commands for managing data:

### Reddit Parsing

```bash
# Parse new scoreposts from Reddit (primary command)
php artisan parse:reddit

# Parse a single post by ID
php artisan parse:reddit --single=abc123

# Archive historical posts
php artisan parse:reddit --archive

# Archive top posts
php artisan parse:reddit --archive-top --top-by=week

# Update existing posts (refresh scores)
php artisan parse:reddit --update --update-min-score=100
```

### Score & Ranking Updates

```bash
# Update all player scores (or a specific player)
php artisan parse:player:score
php artisan parse:player:score {player_id}

# Update all author scores (or a specific author)
php artisan parse:author:score
php artisan parse:author:score {author_name}

# Save current player rankings to history
php artisan parse:ranks
php artisan parse:ranks {timestamp}
```

### Data Synchronization

```bash
# Sync authors table from posts
php artisan sync:authors

# Fetch player aliases (previous usernames) from osu! API
php artisan parse:aliases
```

### Daily Game

```bash
# Create daily game for today (UTC)
php artisan game:create-daily

# Create daily game for a specific date
php artisan game:create-daily --date=2024-01-15
```

## Scheduled Tasks

The application uses Laravel's task scheduler. Add this cron entry to your server:

```bash
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

The following tasks are scheduled automatically:

| Command | Schedule | Description |
|---------|----------|-------------|
| `parse:reddit` | Every 5 minutes | Fetch new scoreposts from Reddit |
| `parse:ranks` | Daily at 3:00 AM | Save player rankings snapshot |
| `parse:player:score` | Daily at 3:10 AM | Recalculate all player scores |
| `parse:author:score` | Daily at 3:40 AM | Recalculate all author scores |
| `game:create-daily` | Daily at midnight UTC | Create the daily guessing game |

## Code Quality

```bash
# Check code style (PSR-12)
composer phpcs

# Auto-fix code style issues
composer phpcbf

# Run static analysis
composer phpstan

# Run tests
vendor/bin/phpunit
```

## Problems

If you can't find certain scoreposts, then there are severeal reasons why:
- The Player had a namechange before the launchdate of ppvr (17.09.2018)
- The Player got banned on osu! before the launchdate
- The title of the scorepost is badly formatted
- The player's total score is <100
- My script did something wrong

Most scoreposts that are missing are still saved in the database but in another table, so I can review them manually over time. If you want certain scorepost to be visible sooner, you can contact me through the methods mentioned down below and I will prioritize your request.

## Acknowledgements
Big thanks to pushshift.io, which made it possible to archive old scoreposts. Also thanks to [christopher-dG](https://github.com/christopher-dG) for making osu-bot, where I took some regular expressions and constants from.
### Resources:
- [Eva Icons](https://akveo.github.io/eva-icons/)

## Contact
You can contact me on [Reddit](https://www.reddit.com/message/compose?to=Andruz) or [osu!](https://osu.ppy.sh/home/messages/users/2924006)
