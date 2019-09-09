@extends('layouts.main')

@section('content')

    <h1>Changelog</h1>
    <br>

    <div class="accordion" id="changelogAccordion">
        <div class="card">
            <div class="card-header" id="headingOne">
                <h2 class="mb-0">
                    <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                        2019-09-xx
                    </button>
                </h2>
            </div>

            <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#changelogAccordion">
                <div class="card-body">
                    <p class="nunito">
                        <span class="badge badge-primary">New</span> This changelog page.<br>
                    </p>
                    <p class="nunito">
                        <span class="badge badge-success">Fix</span> Versions are now identified by the date. Not sure why I thought that naming versions after latin pumpkin names was cool.<br>
                    </p>
                    <p class="nunito">
                        <span class="badge badge-secondary">Misc</span> Ranking now shows 50 entries on one page instead of 15.<br>
                    </p>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header" id="headingOne">
                <h2 class="mb-0">
                    <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                        201907vAMPHORA
                    </button>
                </h2>
            </div>

            <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#changelogAccordion">
                <div class="card-body">
                    <p class="nunito">
                        <span class="badge badge-primary">New</span> This changelog page.<br>
                        <span class="badge badge-primary">New</span> About page.
                    </p>
                    <p class="nunito">
                        <span class="badge badge-success">Fix</span> Images linked on scoreposts are shown again on the post-pages.<br>
                        <span class="badge badge-success">Fix</span> Prettier error message when a user is not found when searching.<br>
                        <span class="badge badge-success">Fix</span> Ranking was outright broken, using "score" as the main unit. The problem is, that "score" is a sum of upvotes and downvotes. We don't want that. It is now (upvotes - downvotes). Thanks to <a href="https://www.youtube.com/channel/UCW6ab2mNEnCU9nmtZzn27qg">IceCandle</a> for reporting this :)
                    </p>
                    <p class="nunito">
                        <span class="badge badge-secondary">Misc</span> Ranking now shows 50 entries on one page instead of 15.<br>
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection