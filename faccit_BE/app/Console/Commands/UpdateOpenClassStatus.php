<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UpdateOpenClassStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:update-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the status of professor attendance records after the end time';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();

        // Update the status of records where the end_time is in the past
        DB::table('professor_attendances')
            ->where('status', 'Open')
            ->whereRaw('DATE(date) = ?', [$now->format('Y-m-d')])
            ->whereRaw('TIME(end_time) < ?', [$now->format('H:i:s')])
            ->update(['status' => 'Closed']);

        $this->info('Professor attendance status updated successfully.');
    }


}
