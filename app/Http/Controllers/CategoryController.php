<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Category;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use function PHPSTORM_META\map;
use function PHPUnit\Framework\isEmpty;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::where('is_active',1)
            ->select('id','name_en', 'name_ar', 'image')
            ->get()
            ->map(function ($category) { // Ensure each image path is converted to a full URL
                $category->image = url($category->image);
                return $category;
            });

        return $this->api_response(true,'Retrieving categories data successfully',$categories);
    }

    public function ItemsCategory($id = 0)
{
    // Subquery
    $subItemDiscount = DB::table('discount_items')
        ->join('discounts', 'discount_items.discount_id', '=', 'discounts.id')
        ->whereIn('discounts.status', ['new', 'active'])
        ->whereDate('discounts.start_date', '<=', now())
        ->whereDate('discounts.end_date', '>=', now())
        ->select('discount_items.item_id', DB::raw('MAX(discounts.percentage) as max_item_discount'))
        ->groupBy('discount_items.item_id');

    $subCategoryDiscount = DB::table('discount_categories')
        ->join('discounts', 'discount_categories.discount_id', '=', 'discounts.id')
        ->join('categories', 'discount_categories.category_id', '=', 'categories.id')
        ->join('items', 'categories.id', '=', 'items.category_id')
        ->whereIn('discounts.status', ['new', 'active'])
        ->whereDate('discounts.start_date', '<=', now())
        ->whereDate('discounts.end_date', '>=', now())
        ->select('items.id as item_id', DB::raw('MAX(discounts.percentage) as max_category_discount'))
        ->groupBy('items.id');


    $items = DB::table('items')
        ->leftJoinSub($subItemDiscount, 'item_discounts', function ($join) {
            $join->on('items.id', '=', 'item_discounts.item_id');
        })
        ->leftJoinSub($subCategoryDiscount, 'category_discounts', function ($join) {
            $join->on('items.id', '=', 'category_discounts.item_id');
        })
        ->when($id, function ($query) use ($id) {
            $query->where('items.category_id', '=', $id);
        })
        ->select(
            'items.id',
            'items.name_en',
            'items.name_ar',
            'items.description_en',
            'items.description_ar',
            'items.image',
            'items.price as original_price',
            DB::raw('
                (items.price - (items.price * (
                    GREATEST(
                        IFNULL(item_discounts.max_item_discount, 0) / 100,
                        IFNULL(category_discounts.max_category_discount, 0) / 100
                    )
                ))) as final_price
            ')
        )
        ->orderByDesc('items.created_at')
        ->get();


    $data = $items->map(function ($item) {
        return [
            'id' => $item->id,
            'name_en' => $item->name_en,
            'name_ar' => $item->name_ar,
            'description_en' => $item->description_en,
            'description_ar' => $item->description_ar,
            'image' => $item->image ? asset("storage/{$item->image}") : asset('images/default.png'),
            'original_price' => round($item->original_price, 2),
            'final_price' => round($item->final_price, 2),
        ];
    });

    return $this->api_response(true, 'Items returned successfully', $data);
}


}
