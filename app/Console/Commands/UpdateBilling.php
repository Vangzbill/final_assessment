<?php

namespace App\Console\Commands;

use App\Http\Controllers\Api\BillingController;
use App\Models\BillingRevenue;
use App\Models\KontrakNodelink;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateBilling extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-billing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            DB::beginTransaction();

            $monthOldNodelinks = BillingRevenue::with(['order'])->where('status', 'Paid')
                ->where('jatuh_tempo', '<=', Carbon::now()->endOfMonth())
                ->whereNotNull('bukti_ppn')
                ->whereHas('order', function ($query) {
                    $query->whereNotNull(('nama_node'));
                })
                ->get();

            $count = 0;
            foreach ($monthOldNodelinks as $nodelink) {
                // $existingBilling = BillingRevenue::where(function($query) use ($nodelink) {
                //     $query->where('kontrak_nodelink_id', $nodelink->id)
                //           ->orWhere('order_id', $nodelink->kontrak_layanan->kontrak->order_id);
                // })->exists();

                // if (!$existingBilling) {
                $ppn = round($nodelink->total_biaya * 0.11);
                $totalAkhir = $nodelink->total_biaya + $ppn;

                BillingRevenue::create([
                    'kontrak_nodelink_id' => $nodelink->id,
                    'order_id' => $nodelink->kontrak_layanan->kontrak->order_id,
                    'tanggal_tagih' => Carbon::now()->startOfMonth(),
                    'total_tagihan' => $nodelink->total_biaya,
                    'total_ppn' => $ppn,
                    'total_akhir' => $totalAkhir,
                    'jatuh_tempo' => Carbon::now()->endOfMonth(),
                    'status' => 'Unpaid'
                ]);

                $count++;
                // }
            }

            DB::commit();

            $message = "Berhasil membuat {$count} billing revenue baru";
            Log::info($message);
            $this->info($message);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error saat membuat billing revenue: " . $e->getMessage());
            $this->error("Terjadi kesalahan: " . $e->getMessage());
        }
    }
}
