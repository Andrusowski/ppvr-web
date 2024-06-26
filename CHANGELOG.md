# Changelog

All notable changes to this project will be documented in this file.

## 2024-06-30
### Added
- Added a basic trend indicator for the general stats on the highlights page

### Fixed
- Fixed the top comments not displaying on the homepage

## 2024-04-11
### Added
- Added a ratelimiter to the reddit client
- Added a simple search modal to select between player and author search results (limited to 1 each for now)

## 2024-04-09
### Added
- Added CLI commands for crawling reddit users or importing a single post

### Changed
- Replaced redis alerts with an env variable

## 2024-04-01
### Changed
- Changed highlights page to use UTC time

## 2024-03-29
### Added
- Added a way to set an alert banner in redis
- Added an option to the post parsing command to be able to parse the top posts of a given timeframe (e.g. year, month, week)

## 2024-01-30
### Fixed
- Fixed the trigger for updating an existing post 

## 2024-01-23
### Added
- Added animation to the Title on the index page

### Changed
- Changed the look of most of the table elements

### Removed
- Removed awards from profile pages

## 2023-12-01
### Added
- Added oauth authentication to all reddit api requests

### Changed
- Refactored the scorepost parsing logic to use the reddit comment api for each post
- Added an update command to be able to update old scorepost scores

### Removed
- Removed the gameplay flair requirement for scoreposts since the new fetching algorithm is not limited by the amount of the search api results

## 2023-11-06
### Added
- Added a highlight function to create a highlight post for the previous month
- Added separate weighted author score values
- Added ranking- and post caching to index page

### Changed
- Changed styling of most elements with sharp corners

## 2023-10-31
### Changed
- Changed author rankings to a weighted system like previously done with player rankings
- Replaced the discord link in the footer with an osu! profile link

### Fixed
- Fixed top comments not loading on the home and post pages

## 2022-08-02
### Changed
- Api docs
- Added commands to scheduler to prevent database lock timeouts when parsing new posts while ranks are being recalculated

### Added
- Posts by player id API endpoint

## 2022-05-18
### Changed
- Replaced the rank change indicator on the player ranking page with a recent activity indicator due to a lot of problems with it
- Fixed a minor bug regarding the player page
- Minor code refactoring

## 2022-04-29
### Added
- Basic API for posts, players and rank history
- API Documentation page and navigation link
- Sentry error logging

### Changed
- Upgraded Vue to v3
- Upgraded Chart.js to v3
- Refactored osu! and Reddit API functions
    - Added clients as an abstraction layer for api calls
    - Changed osu! api calls to work with osu!api v2
    - Alias creation is now based on the previous_usernames attribute in the user api result instead of the player's scraped page
- Rank changes on the ranking page are now compared to the past day instead of the past week

## 2020-07-03
### Added
- Stats page
- FAQ page
- Rank change indicator on player ranking page

### Changed
- Javascript rewrite using Vue components to render graphs
- Improved ranking pagination by adding page numbers
- Updated dependencies

## 2020-02-23
Note: This version is a major rewrite and took a bunch of time because of procrastination :^) it's possible that not everything is documented for this version

### Added
- Graphs showing the player's rank history
- Maximizable scorepost screenshots on post pages.
- Silver, gold and platnium on detail pages.
- Artisan command for saving player ranks periodically.
- Artisan command for getting the old names of players.
- Artisan command for updating scores after manual changes to posts.
- Placeholders for a statistics and an FAQ Page

### Changed
- **Player PP is now weighted** using the same calculation the official PP uses
- Quotes on the start page can now be clicked to get to the originating post
- Transition from bootstrap to uikit
- ppvr-bot is now archived and was replaced by Laravel Artisan commands.
- Aliases are now stored forever. Instead of being a column in the player database table, aliases now have a seperate table so it is possible to save multiple aliases per player.
- The changelog is now this Markdown file instead of a page on the site.


## 2019-07-14 

### Added
- A changelog
- About page

### Changed
- Images linked on scoreposts are shown again on the post-pages.
- Prettier error message when a user is not found when searching.
- Ranking was outright broken, using "score" as the main unit. The problem is, that "score" is a sum of upvotes and downvotes. We don't want that. It is now (upvotes - downvotes). Thanks to IceCandle for reporting this :)
- Ranking now shows 50 entries on one page instead of 15.


