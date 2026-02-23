# AGENTS.md - Coding Agent Guidelines for PPvR-Web

This document provides guidelines for AI coding agents working on this Laravel 10 + Vue.js 3 codebase.

## Project Overview

PPvR (PP via Reddit) is an alternative osu! ranking system that parses scoreposts from /r/osugame and calculates player rankings. Backend is PHP/Laravel, frontend is Vue.js 3 with Laravel Mix.

## Build Commands

### PHP (Backend)
```bash
composer install                    # Install dependencies
php artisan serve                   # Start development server
php artisan migrate                 # Run database migrations
php artisan tinker                  # Interactive REPL
```

### JavaScript (Frontend)
```bash
npm install                         # Install dependencies
npm run dev                         # Development build
npm run watch                       # Watch mode with auto-rebuild
npm run hot                         # Hot module replacement
npm run prod                        # Production build
```

## Linting and Code Style

### PHP Code Sniffer (PHPCS)
```bash
composer phpcs                      # Check code style
composer phpcbf                     # Auto-fix code style issues
```

### Static Analysis
```bash
composer phpstan                    # Run PHPStan analysis on app/ and routes/
```

## Testing

### Running Tests
```bash
vendor/bin/phpunit                              # Run all tests
vendor/bin/phpunit tests/Unit/ExampleTest.php   # Run single test file
vendor/bin/phpunit --filter testMethodName      # Run single test method
vendor/bin/phpunit --testsuite Unit             # Run Unit test suite
vendor/bin/phpunit --testsuite Feature          # Run Feature test suite
```

### Test Configuration
- Framework: PHPUnit 9.3.3
- Config: `phpunit.xml`
- Test suites: `tests/Unit` and `tests/Feature`

## Code Style Guidelines

### PHP Standards
This project follows PSR-2/PSR-12 with Slevomat Coding Standard extensions. Key rules:

**Imports:**
- Alphabetically sorted use statements (enforced)
- No leading backslash on use statements
- Remove unused imports
- Group by type is not required

```php
use App\Models\Player;
use App\Services\Clients\OsuClient;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
```

**Formatting:**
- 4-space indentation (no tabs)
- LF line endings
- UTF-8 charset
- Trailing array commas required
- No Yoda comparisons (use `$var === 'value'` not `'value' === $var`)
- Modern array syntax `[]` not `array()`
- Use `&&` and `||` instead of `and` and `or`

**Type Hints:**
- Use short type hints (`int` not `integer`, `bool` not `boolean`)
- Nullable types for null default values
- Return type hints are encouraged

**Classes:**
- Class constants must have visibility modifiers
- Use `new ClassName()` with parentheses always
- Use `static::class` or `self::class` instead of `__CLASS__`

### Naming Conventions

**PHP:**
- Classes: `PascalCase` (e.g., `PlayerController`, `RedditParser`)
- Methods: `camelCase` (e.g., `getIndex`, `updatePlayerScore`)
- Variables: `camelCase` (e.g., `$playerIds`, `$accessToken`)
- Constants: `SCREAMING_SNAKE_CASE` (e.g., `TIME_FIRST_POST`)
- Properties: `camelCase` with visibility modifiers

**Files:**
- Controllers: `{Name}Controller.php`
- Models: `{Name}.php` (singular)
- Services: `{Name}Service.php` or `{Name}Client.php`
- Commands: `{VerbNoun}.php`

**JavaScript/Vue:**
- Components: `PascalCase.vue`
- Variables/functions: `camelCase`

### Directory Structure
```
app/
├── Console/Commands/     # Artisan commands
├── Exceptions/           # Custom exceptions
├── Http/
│   ├── Controllers/      # Web controllers
│   ├── Middleware/       # HTTP middleware
│   └── Resources/        # API resources
├── Models/               # Eloquent models
│   ├── Api/              # API DTOs (non-Eloquent)
│   └── Cache/            # Cache-related models
├── Providers/            # Service providers
└── Services/
    ├── Clients/          # External API clients (OsuClient, RedditClient)
    └── Controller/       # Controller service classes
```

## Error Handling Patterns

### Sentry Integration
Exceptions are automatically reported to Sentry when configured.

### Common Patterns
```php
// Return empty object on API failure
try {
    $response = $this->client->get($endpoint);
} catch (ClientException $exception) {
    return new User();  // Return empty DTO
}

// Abort for HTTP errors
if (!$player) {
    abort(404);
}

// Custom exceptions
throw new PostNotFoundException($postId);
```

## Service Layer Pattern

This codebase uses a service layer to separate business logic from controllers:

- **Controller Services:** `PlayerControllerService`, `RankingControllerService`
- **API Clients:** `OsuClient`, `RedditClient` - wrap external API calls
- **Domain Services:** `ScoreService`, `RedditParser` - complex business logic

Controllers should be thin, delegating to services:
```php
public function getIndex($id)
{
    $controller = new PlayerControllerService();
    $player = Player::find($id);
    // ... delegate to service methods
}
```

## Vue.js Patterns

- Vue 3 with Composition API
- Use `inject()` for dependency injection
- Use `ref()` for reactive state
- Props for component configuration

## CI/CD

GitHub Actions runs on push/PR to `master`:
1. PHP 8.1 setup
2. Composer install
3. Database migrations
4. Code style check (`composer phpcs`)

## EditorConfig

The project uses `.editorconfig` for consistent formatting:
- UTF-8 charset
- LF line endings
- 4-space indentation (2 for YAML)
- Trim trailing whitespace
- Insert final newline

## Environment

- PHP: ^8.1
- Laravel: 10.x
- Vue.js: 3.x
- Node.js: Compatible with Laravel Mix 6
- Database: SQLite (dev), configurable via `.env`

## Important Notes

1. Always run `composer phpcs` before committing to ensure code style compliance
2. Use `composer phpcbf` to auto-fix most style issues
3. The frontend has no ESLint/Prettier - maintain consistency manually
4. External API calls (osu!, Reddit) have rate limiting built into clients
5. Models in `app/Models/Api/` are DTOs, not Eloquent models
