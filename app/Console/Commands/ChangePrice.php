<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Modules\ThmartApi\Models\OrdersSku;
use App\Modules\ThmartApi\Models\CrontabChangePrice;
use App\Modules\ThmartApi\Models\OrdersSpell;

/*
 *  定时调整订单商品成本价
 */
class ChangePrice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:changePrice';

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
        $CrontabChangePrice = new CrontabChangePrice;
        $ordersSku = new OrdersSku;
        //获取一天内所有被修改过的供应商id
        $res = $CrontabChangePrice->getSupplierIdArray();
        //调整所有涉及到这些供应商id的订单商品成本价
        if (isset($res) && $res) $ordersSku->changePrice($res);
    }
}
