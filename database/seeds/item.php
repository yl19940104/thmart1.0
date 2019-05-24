<?php

use Illuminate\Database\Seeder;

class item extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('item')->insert([
        	['shopId' => 1, 'type' => 1, 'categoryName' => 2, 'brandName' => 1, 'productNo' => 1, 'title' => 3333333, 'propName' => 1, 'subTitle' => 1, 'imageNumber' => 1, 'detail' => 1, 'isOnline' => 1, 'onLineTime' => 1, 'offLineTime' => 1, 'shippingTemplateId' => 1, 'shippingFee' => 1, 'precentageId' => 1, 'unAudited' => 1, 'isDelete' => 1, 'sellNumber' => 1, 'minPrice' => 1, 'coverpic' => 'https://api.mall.thatsmags.com/Public/ckfinder/images/flower/IOU/主图1.jpg', 'createTime' => 1]
        ]);
    }
}
