<?php

namespace App\Console;

use App\Models\Branch;
use App\Models\Closing;
use App\Models\Order;
use App\Models\OrderHistory;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        $schedule->command('snapshot:create ' . time())->hourly();
        $schedule->command('snapshot:cleanup --keep=5')->hourly();
        $schedule->call(function () {
            $branches = Branch::all();
            foreach ($branches as $key => $branch) :
                $payments = getPaidTotalByMode(Carbon::today()->startOfDay(), Carbon::today()->endOfDay(), $branch->id, $mode = [1]);
                $expense = getExpenseTotal(Carbon::today(), Carbon::today(), $branch->id);
                $income = getIncomeTotalByMode(Carbon::today(), Carbon::today(), $branch->id, $mode = [1]);
                $bank = getBankTransferTotal(Carbon::today()->startOfDay(), Carbon::today()->endOfDay(), $branch->id);
                $opening_balance = getOpeningBalance(Carbon::today()->subDay(), $branch->id);
                $voucher_total_receipt = getVoucherTotal(Carbon::today()->startOfDay(), Carbon::today()->endOfDay(), $branch->id, $type = 'receipt', $mode = [1]);
                $voucher_total_payment = getVoucherTotal(Carbon::today()->startOfDay(), Carbon::today()->endOfDay(), $branch->id, $type = 'payment', $mode = [1]);
                Closing::insert([
                    'date' => Carbon::today(),
                    'closing_balance' => ($opening_balance + $payments + $income + $voucher_total_receipt) - ($expense + $bank + $voucher_total_payment),
                    'branch' => $branch->id,
                    'closed_by' => 1,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            endforeach;
        })->dailyAt('23:45');

        $schedule->call(function () {
            OrderHistory::whereIn('order_id', Order::where('order_status', 'delivered')->whereDate('invoice_generated_at', '<=', Carbon::now()->subDays(7))->pluck('id'))->forcedelete();
        })->dailyAt('23:45');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
