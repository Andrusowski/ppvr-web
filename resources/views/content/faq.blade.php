@extends('layouts.main')

@section('content')

    <h1 class="uk-heading-small uk-text-center">FAQ</h1>

    <ul uk-accordion class="uk-card uk-card-default uk-card-hover uk-padding">
        <li class="uk-open">
            <a class="uk-accordion-title" href="#">What is PPvR?</a>
            <div class="uk-accordion-content">
                <p>The idea behind PPvR is based on <a href="https://twitter.com/bahamete/status/919625209619079170?lang=en">this tweet</a>. The tweet describes this pretty well. </p>
                <p>Data is fetched every 5 minutes from scoreposts on the subreddit <a href="https://www.reddit.com/r/osugame/">r/osugame</a>. The up- and downvotes are then used to calculate a player's "pp" and their rank.</p>
            </div>
        </li>
        <li>
            <a class="uk-accordion-title" href="#">What is a scorepost and what should it look like?</a>
            <div class="uk-accordion-content">
                <p>To make sure that your scorepost is correctly parsed by PPvR, the title should look something like this:</p>
                <p><span class="uk-text-primary">Player Name</span> | <span class="uk-text-success">Song Artist</span> - <span class="uk-text-warning">Song Title</span> [<span class="uk-text-danger">Diff Name</span>] +Mods whateveryouwant</p>
                <p>For example:</p>
                <p><span class="uk-text-primary">Cookiezi</span> | <span class="uk-text-success">xi</span> - <span class="uk-text-warning">FREEDOM DiVE</span> [<span class="uk-text-danger">FOUR DIMENSIONS</span>] +HDHR 99.83% FC 800pp *NEW PP RECORD*</p>
                <p>The +Mods part is irrelevant for PPvR, but other software like <a href="https://github.com/christopher-dG/osu-bot">osu-bot</a> rely on this information.</p>
                <p>PPvR tries to filter out common Prefixes like "UNNOTICED?", "OFFLINE" or Gamemodes like "OSU!TAIKO", but something unique like "HOLYSHITGODMODE??!1" will likely add your post to the bin of invalid scoreposts. If you feel like adding additional information to your post title, then please add it after the [Diff Name] since that information is not parsed at all.</p>
                <p>For more help, check out <a href="https://www.youtube.com/watch?v=igP42gDnYrc">this video</a> for a visual guide how to create a scorepost.</p>
            </div>
        </li>
        <li>
            <a class="uk-accordion-title" href="#">My scorepost was not recognized by PPvR. Can I request it to be added manually?</a>
            <div class="uk-accordion-content">
                <p>There are thousands of scoreposts that are not added to the ranking because the title can't be parsed. I decided to not add certain scoreposts manually, since it is a ton of work to do and would be kinda unfair if I would do it only for certain posts.</p>
                <p>So if you think that your scorepost is formatted correctly and should be displayed here, then feel free to contact me.</p>
                <p>I try to make the parsing process more accurate from time to time. When changes to the process happen, all scoreposts in the past will be re-parsed to add missing ones.</p>
            </div>
        </li>
        <li>
            <a class="uk-accordion-title" href="#">How accurate is this ranking?</a>
            <div class="uk-accordion-content">
                <p>Even though there thousands of invalid scoreposts not taken into account when calculating the ranking, it is still fairly accurate. The reason for that being, that most of the invalid scoreposts have rather poor scores and are therefore not that influential for most players' ranks (only around 17% of invalid scoreposts have a score higher than 100). The quantity of these smaller scoreposts is also pretty irrelevant since they become even more insignificant after the weighting is applied.</p>
                <p>Although you should consider that this is a ranking based on reddit upvotes. It may accurately display a player's presence on the subreddit, but it does not represent skill. Popularity is probably the primary part of a player's rank here.</p>
            </div>
        </li>
        <li>
            <a class="uk-accordion-title" href="#">Are the scores weighted?</a>
            <div class="uk-accordion-content">
                <p>The individual scores displayed on a player's page are not weighted. They are simply calculated by subtracting downvotes from the upvotes and adding a certain boost depending on the amount of awards a post got.</p>
                <p>However, the scores on the ranking page are weighted just like they are in osu. The same also applies for author rankings.</p>
            </div>
        </li>
    </ul>


@endsection
