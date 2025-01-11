<?php

namespace App\Jobs;

use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Str;

class SaveMembershipJob implements ShouldQueue
{
    use Queueable;

    private $row;

    /**
     * Create a new job instance.
     */
    public function __construct($row)
    {
        $this->row = $row;
    }

    private function update($row){

    }

    private function insert($row){
        $data = [
            'firstname' => $row[1],
            'lastname' => $row[2],
            'phone' => $row[5],
            'private_phone' => $row[7],
            'gender' => ucfirst($row[9]),
            'address' => $row[12],
            'nationality' => $row[13],
            'photo' => "https://avatars.dicebear.com/api/initials/" . $row[1].' '.$row[2] . ".svg",
            'dob' => Carbon::createFromFormat('m/d/Y', $row[8])->format('Y-m-d'),
            'email' => $row[4],
        ];
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        
    }
}
