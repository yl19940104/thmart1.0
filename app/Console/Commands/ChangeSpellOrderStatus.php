<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\OrdersSku;
use App\Modules\ThmartApi\Models\CrontabChangePrice;
use App\Modules\ThmartApi\Models\OrdersSpell;

/*
 *  定时把拼单未满的订单改成拼单成功状态
 */
class ChangeSpellOrderStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:changeSpellOrderStatus';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
       /* $time = time();
        $time = date('Y-m-d H:i:s', $time);
        DB::table('test')->insert(['test'=>1, 'time'=>$time]);*/
        $ordersSpell = new OrdersSpell();
        $ordersSpell->changeStatusToSeven();
    }
}
