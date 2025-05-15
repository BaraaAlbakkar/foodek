<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Discount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use function PHPUnit\Framework\isEmpty;

class DiscountController extends Controller
{
    public function offers()
    {
        $offers = Discount::where(function($query){
            $query->whereHas('categories')
                ->orWhereHas('items');
        })
        ->whereIn('status', ['new', 'active'])
        ->with(['items:id,name_en,name_ar','categories:id,name_en,name_ar'])
        ->get();

        if($offers->isEmpty()){
            return $this->api_response(false,'There are no offers',[],204);
        }

        return $this->api_response(true,'Returend successfully',$offers);
    }


}
