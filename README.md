# PPvR

[PPvR](https://ppvr.andrus.io/) is an alternative osu! ranking system based on the scores of scoreposts on the game's subreddit [/r/osugame](https://www.reddit.com/r/osugame/).

This is why: https://twitter.com/bahamete/status/919625209619079170


This was originally a university project, and consists of two parts. The first one is this Laravel-based ranking website and the [other](https://github.com/Andrusowski/ppvr-bot) is a vanilla PHP skript which parses new reddit posts and puts them in a database.

## Problems
Everything should be in a usable state, but there are still some minor features like more stats and graphs that I still have to implement.

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
You can contact me on [Discord](https://discordapp.com/users/86760014068355072), [Reddit](https://www.reddit.com/message/compose?to=Andruz) or [osu!](https://osu.ppy.sh/home/messages/users/2924006)
