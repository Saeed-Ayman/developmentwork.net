<?php

namespace App\Console\Commands;

use App\Models\Post;
use Illuminate\Console\Command;

class RemoveSoftDeleteCommand extends Command
{
    protected $signature = 'remove:soft-delete';

    protected $description = 'remove soft-delete posts';

    public function handle()
    {
    }
}
