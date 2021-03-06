# Changelog

All notable changes to this project will be documented in this file.

## 2020-07-03
### Added
- Stats page
- FAQ page
- rank change indicator on player ranking page

### Changed
- Javascript rewrite using Vue components to render graphs
- Improved ranking pagination by adding page numbers
- updated dependencies

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


