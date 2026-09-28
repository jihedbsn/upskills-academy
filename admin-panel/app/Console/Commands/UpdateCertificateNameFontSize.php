<?php

namespace App\Console\Commands;

use App\Models\Certif;
use Illuminate\Console\Command;

class UpdateCertificateNameFontSize extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'certificates:update-name-font-size {--percentage=25 : Percentage to increase font size}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the name_student font size in all certificate templates by a specified percentage';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $percentage = (float) $this->option('percentage');

        if ($percentage <= 0) {
            $this->error('Percentage must be greater than 0');
            return 1;
        }

        $this->info("Updating certificate name font sizes by {$percentage}%...");

        $certifs = Certif::all();
        $updated = 0;

        foreach ($certifs as $certif) {
            $templateData = $certif->template_data ?? [];

            if (isset($templateData['name_student']['font_size'])) {
                $oldSize = (float) $templateData['name_student']['font_size'];
                $newSize = round($oldSize * (1 + ($percentage / 100)), 1);

                $templateData['name_student']['font_size'] = $newSize;
                $certif->template_data = $templateData;
                $certif->save();

                $this->line("Certificate ID {$certif->id}: {$oldSize}px → {$newSize}px");
                $updated++;
            }
        }

        $this->info("\nSuccessfully updated {$updated} certificate template(s)!");
        return 0;
    }
}
