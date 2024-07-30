<?php

namespace App\Console\Commands;

use App\Models\Deposit;
use App\Models\Member;
use Carbon\Carbon;
use Illuminate\Console\Command;

class InsertDataBunga extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:insert-data-bunga';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Insert bunga into the database automatically';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $members = Member::all();
        // return $members;
        $tabungans = [];

        foreach ($members as $member) {
            // $depoBunga = Deposit_types::where('id', 'simpanan sukarela')->get();
            $saldo = Deposit::where('member_id', $member->id)->where('deposit_type_id', 1)->latest()->first();
            if ($saldo) {
                $bunga =  $saldo->saldo * 0.0025;
                // return $bunga;
                $tabungans[] = [
                    'member_id' => $saldo->member_id,
                    'saldo' => $saldo->saldo + $bunga,
                    'interest' => $bunga,
                    'debet' => null,
                    'kredit' => null,
                    'date' => Carbon::now(),
                    'created_by' => 3,
                    'deposit_type_id' => 1,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()


                ];
            }
        }
        Deposit::insert($tabungans);
        $this->info('Data inserted successfully');
    }
}
