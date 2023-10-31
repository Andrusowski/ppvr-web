<?php

namespace App\Console\Commands;

use App\Models\Author;
use App\Models\Post;
use Illuminate\Console\Command;

class SyncAuthors extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:authors';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync authors table based on the authors found in the posts table';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Post::select('author')->distinct()->get()->each(function ($author) {
            $author = Author::whereName($author->author)->firstOrCreate(['name' => $author->author]);
        });

        $this->newLine();
    }
}
