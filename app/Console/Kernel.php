<?php

namespace App\Console;

use App\Models\Branch;
use App\Models\Closing;
use App\Models\Order;
use App\Models\OrderHistory;
use App\Models\PromotionContact;
use App\Models\PromotionSchedule;
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
            $branches = Branch::where('type', 'branch')->get();
            foreach ($branches as $key => $branch) :
                $payments = getPaidTotalByMode(Carbon::today()->startOfDay(), Carbon::today()->endOfDay(), $branch->id, $mode = [1]);
                $expense = getExpenseTotal(Carbon::today(), Carbon::today(), $branch->id, 1);
                $vehicle_payment_total = getVehiclePaymentTotal(Carbon::today()->startOfDay(), Carbon::today()->endOfDay(), $branch->id, $mode = [1]);
                $income = getIncomeTotalByMode(Carbon::today(), Carbon::today(), $branch->id, $mode = [1]);
                $bank = getBankTransferTotal(Carbon::today()->startOfDay(), Carbon::today()->endOfDay(), $branch->id, null);
                $opening_balance = getOpeningBalance(Carbon::today()->subDay(), $branch->id);
                $voucher_total_receipt = getVoucherTotal(Carbon::today()->startOfDay(), Carbon::today()->endOfDay(), $branch->id, $type = 'receipt', $mode = [1]);
                $voucher_total_payment = getVoucherTotal(Carbon::today()->startOfDay(), Carbon::today()->endOfDay(), $branch->id, $type = 'payment', $mode = [1]);
                Closing::insert([
                    'date' => Carbon::today(),
                    'closing_balance' => ($opening_balance + $payments + $income + $voucher_total_receipt) - ($expense + $bank + $voucher_total_payment + $vehicle_payment_total),
                    'branch' => $branch->id,
                    'closed_by' => 1,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            endforeach;
        })->dailyAt('23:30');

        $schedule->call(function () {
            OrderHistory::whereIn('order_id', Order::where('order_status', 'delivered')->whereDate('invoice_generated_at', '<=', Carbon::now()->subDays(7))->pluck('id'))->forcedelete();
        })->dailyAt('23:30');

        $schedule->call(function () {
            $promo = PromotionSchedule::whereDate('scheduled_date', Carbon::today())->where('status', 'publish')->latest()->first();
            if ($promo):
                $clist = PromotionContact::selectRaw("id, name, contact_number as mobile, 'clist' as type")->whereNull('wa_sms_status')->where('entity', $promo->entity)->where('type', 'include')->when($promo->branch_id > 0, function ($q) use ($promo) {
                    return $q->where('branch_id', $promo->branch_id);
                })->orderBy('id');
                $cdata = null;
                if ($promo->entity == 'store'):
                    $cdata = Order::selectRaw("id, name, mobile, 'ord' as type ")->whereNull('wa_sms_status')->when($promo->branch_id > 0, function ($q) use ($promo) {
                        return $q->where('branch_id', $promo->branch_id);
                    })->whereNotIn('mobile', PromotionContact::where('type', 'exclude')->pluck('contact_number'))->limit($promo->sms_limit_per_hour)->union($clist)->orderBy('id')->get()->unique('mobile');
                endif;
                if ($cdata):
                    $ids1 = [];
                    $ids2 = [];
                    foreach ($cdata as $key => $item):
                        sendWaPromotion($promo, $item->name, $item->mobile);
                        if ($item->type == 'clist'):
                            array_push($ids1, $item->id);
                        else:
                            array_push($ids2, $item->id);
                        endif;
                    endforeach;
                    PromotionContact::whereIn('id', $ids1)->update(['wa_sms_status' => 'yes']);
                    Order::whereIn('id', $ids2)->update(['wa_sms_status' => 'yes']);
                endif;
            endif;
        })->hourly();

        $schedule->call(function () {
            $promo = PromotionSchedule::whereDate('scheduled_date', Carbon::today())->where('status', 'publish')->latest()->first();
            $promo->update(['sms_count' => $promo->waSmsProcessedCount()]);
            PromotionContact::update(['wa_sms_status' => null]);
            Order::update(['wa_sms_status' => null]);
        })->dailyAt('23:30');
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
