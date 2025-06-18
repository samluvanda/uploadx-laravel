<?php

namespace UploadX\Commands;

use Illuminate\Console\Command;

class Install extends Command
{
    protected $signature = 'uploadx:install';
    protected $description = 'Install the UploadX package (publishes config file)';

    public function handle(): void
    {
        $this->info('🔧 Publishing UploadX config file...');

        $this->call('vendor:publish', [
            '--tag' => 'uploadx-config',
            '--force' => true,
        ]);

        $this->info('✅ UploadX installed successfully. You can now customize config/uploadx.php');
    }
}
