<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use App\Models\Item;
use Illuminate\Http\Request;

class FilterController extends Controller
{
    public function dataForFilterPage(){
        $minPrice = Item::min('price');
        $maxPrice = Item::max('price');
        $minDiscount = Discount::min('percentage');
        $maxDiscount = Discount::max('percentage');

        $data = [
            'minimum_price' => $minPrice,
            'maximum_price' => $maxPrice,
            'minimum_percentage' => $minDiscount,
            'maximum_percentage' => $maxDiscount
        ];

        return response()->json([
            'success' => true,
            'message' => 'Data for filter page',
            'data' => $data,
        ],200);
    }

}
