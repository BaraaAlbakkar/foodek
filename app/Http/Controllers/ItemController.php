<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ItemController extends Controller
{
    public function show($id)
    {
        $item = DB::table('items')
            ->join('categories', 'items.category_id', '=', 'categories.id')

            // Join with discount_items pivot and discounts
            ->leftJoin('discount_items', 'items.id', '=', 'discount_items.item_id')
            ->leftJoin('discounts as item_discounts_data', function ($join) {
                $join->on('discount_items.discount_id', '=', 'item_discounts_data.id')
                    ->whereIn('item_discounts_data.status', ['active', 'new'])
                    ->whereDate('item_discounts_data.start_date', '<=', now())
                    ->whereDate('item_discounts_data.end_date', '>=', now());
            })

            // Join with discount_categories pivot and discounts
            ->leftJoin('discount_categories', 'categories.id', '=', 'discount_categories.category_id')
            ->leftJoin('discounts as category_discounts', function ($join) {
                $join->on('discount_categories.discount_id', '=', 'category_discounts.id')
                    ->whereIn('category_discounts.status', ['active', 'new'])
                    ->whereDate('category_discounts.start_date', '<=', now())
                    ->whereDate('category_discounts.end_date', '>=', now());
            })

            // Join through order_items to get reviews
            ->leftJoin('order_items', 'items.id', '=', 'order_items.item_id')
            ->leftJoin('reviews', 'order_items.order_id', '=', 'reviews.order_id')

            ->select(
                'items.id',
                'items.name_en',
                'items.name_ar',
                'items.description_en',
                'items.description_ar',
                'items.image',
                'items.price',
                DB::raw('GREATEST(
                            COALESCE(MAX(item_discounts_data.percentage), 0),
                            COALESCE(MAX(category_discounts.percentage), 0)
                        ) as max_discount_percentage'),
                DB::raw('ROUND(AVG(reviews.rating), 1) as average_rating'),
                DB::raw('COUNT(reviews.id) as review_count')
            )
            ->where('items.id', $id)
            ->groupBy(
                'items.id',
                'items.name_en',
                'items.name_ar',
                'items.description_en',
                'items.description_ar',
                'items.image',
                'items.price'
            )
            ->first();

        if (!$item) {
            return $this->api_response(false,'Item not found',[],404);
        }

        // Calculate the final discounted price
        $discountAmount = $item->price * ($item->max_discount_percentage / 100);
        $discountPrice = round($item->price - $discountAmount, 2);

        // Fetch item options using the item_options pivot
        $options = DB::table('item_options')
            ->join('options', 'item_options.option_id', '=', 'options.id')
            ->where('item_options.item_id', $id)
            ->select('options.id', 'options.name_en', 'options.name_ar', 'options.extra_price', 'options.type', 'options.range')
            ->get()
            ->groupBy('type');

            $data = [
                'id' => $item->id,
                'name_en' => $item->name_en,
                'name_ar' => $item->name_ar,
                'description_en' => $item->description_en,
                'description_ar' => $item->description_ar,
                'image' => $item->image ? asset("storage/{$item->image}") : asset('images/default.png'),
                'rating' => $item->average_rating ?? 0,
                'review_count' => $item->review_count,
                'price' => $item->price,
                'discount_price' => $discountPrice,
                'quantity' => 1,
                'options' => [
                    'addition' => $options->get('addition', collect())->map(fn($option) => [
                        'id' => $option->id,
                        'name_en' => $option->name_en,
                        'name_ar' => $option->name_ar,
                        'extra_price' => $option->extra_price,
                    ]),
                    'flavor' => $options->get('flavor', collect())->map(fn($option) => [
                        'id' => $option->id,
                        'name_en' => $option->name_en,
                        'name_ar' => $option->name_ar,
                        'extra_price' => $option->extra_price,
                        'range' => json_decode($option->range, true) ?? [],
                    ]),
                    'removed' => $options->get('removed', collect())->map(fn($option) => [
                        'id' => $option->id,
                        'name_en' => $option->name_en,
                        'name_ar' => $option->name_ar,
                    ]),
                ],
            ];

        return $this->api_response(true,'Item details fetched successfully.',$data);
    }

//     public function topRated($id = 0){
//         $queryItems = DB::table('items')
//         ->join('order_items','items.id','=','order_items.item_id')
//         ->join('orders','orders.id','=','order_items.order_id')
//         ->join('reviews','reviews.order_id','=','orders.id')
//         ->leftJoin('discount_items','items.id','=','discount_items.item_id')
//         ->leftJoin('discounts',function ($join) {
//             $join->on('discount_items.discount_id', '=', 'discounts.id')
//                 ->whereIn('discounts.status', ['new', 'active'])
//                 ->whereDate('discounts.start_date', '<=', now())
//                 ->whereDate('discounts.end_date', '>=', now());
//         })
//         ->leftJoin('categories', 'items.category_id', '=', 'categories.id')
//         ->leftJoin('discount_categories', 'categories.id', '=', 'discount_categories.category_id')
//         ->leftJoin('discounts as category_discounts', function ($join) {
//             $join->on('discount_categories.discount_id', '=', 'category_discounts.id')
//                 ->whereIn('category_discounts.status', ['active', 'new'])
//                 ->whereDate('category_discounts.start_date', '<=', now())
//                 ->whereDate('category_discounts.end_date', '>=', now());
//         })
//         ->select(
//             'items.id','items.image','items.name_en','items.name_ar','items.description_en',
//             'items.description_ar','items.price as original_price',
//             DB::raw('
//                 (items.price - (items.price * (
//                 GREATEST(
//                 IF(discounts.status IN ("new", "active"), IFNULL(discounts.percentage, 0.00) / 100, 0),
//                 IF(category_discounts.status IN ("new", "active"), IFNULL(category_discounts.percentage, 0.00) / 100, 0)
//             )
//                 ))) as final_price
//             '),
//             DB::raw('IFNULL(ROUND(AVG(reviews.rating), 1), 0) as review_rating'),
//             DB::raw('COUNT(reviews.id) as total_reviews'),
//             'discounts.status as item_discount_status',
//             'category_discounts.status as category_discount_status'
//         );
//         // ->whereNotNull('reviews.rating');

//     if ($id != 0) {
//         $queryItems->where('items.category_id', '=', $id);
//     }

//     $items = $queryItems->groupBy(
//             'items.id','items.image','items.name_en','items.description_en',
//             'items.name_ar','items.description_ar','items.price',
//             'discounts.status','discounts.percentage',
//             'category_discounts.status','category_discounts.percentage'
//         )
//         ->orderByDesc('review_rating')
//         ->orderByDesc('total_reviews')
//         ->take(10)
//         ->get();

//     if ($items->isEmpty()) {
//         return $this->api_response(false,'No items rated yet',[],204);
//     }

//     $data = $items->map(function ($item) {
//         return [
//             'id' => $item->id,
//             'name_en' => $item->name_en,
//             'name_ar' => $item->name_ar,
//             'description_en' => $item->description_en,
//             'description_ar' => $item->description_ar,
//             'image' => $item->image ? asset("storage/{$item->image}") : asset('images/default.png'),
//             'final_price' => round($item->final_price, 2),
//             'rating' => $item->review_rating ?? 0,
//             'total_reviews' => $item->total_reviews,
//         ];
//     });

//     return $this->api_response(true,'Top rated item based on specific category.',$data);
// }

    public function Recommended($id = 0){
        $itemQuery = DB::table('carts')
            ->join('items','carts.item_id','=','items.id')
            ->select('items.id','items.image','items.name_en','items.name_ar',
            DB::raw('COUNT(carts.item_id) as frequent'))
            ->whereNotNull('carts.deleted_at');

        if($id != 0){
            $itemQuery->where('items.category_id','=',$id);
        }

        $items = $itemQuery->groupBy('items.id','items.image','items.name_en','items.name_ar')
                ->orderByDesc('frequent')
                ->take(10)
                ->get();

        if($items->isEmpty()){
            return $this->api_response(false,'There are no items in old carts',[],204);
        }

        return $this->api_response(true,'Recommended items',$items);

    }

    public function topRated($id = 0) {
        $reviewSub = DB::table('reviews')
            ->join('orders', 'reviews.order_id', '=', 'orders.id')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->select(
                'order_items.item_id',
                DB::raw('ROUND(AVG(reviews.rating), 1) as review_rating'),
                DB::raw('COUNT(reviews.id) as total_reviews')
            )
            ->groupBy('order_items.item_id');

        // Get max discount from item discounts
        $itemDiscounts = DB::table('discount_items')
            ->join('discounts', function ($join) {
                $join->on('discount_items.discount_id', '=', 'discounts.id')
                    ->whereIn('discounts.status', ['new', 'active'])
                    ->whereDate('discounts.start_date', '<=', now())
                    ->whereDate('discounts.end_date', '>=', now());
            })
            ->select('discount_items.item_id', DB::raw('MAX(discounts.percentage) as item_discount_percentage'))
            ->groupBy('discount_items.item_id');

        // Get max discount from category discounts
        $categoryDiscounts = DB::table('discount_categories')
            ->join('discounts as cat_discounts', function ($join) {
                $join->on('discount_categories.discount_id', '=', 'cat_discounts.id')
                    ->whereIn('cat_discounts.status', ['new', 'active'])
                    ->whereDate('cat_discounts.start_date', '<=', now())
                    ->whereDate('cat_discounts.end_date', '>=', now());
            })
            ->select('categories.id as category_id', DB::raw('MAX(cat_discounts.percentage) as category_discount_percentage'))
            ->join('categories', 'discount_categories.category_id', '=', 'categories.id')
            ->groupBy('categories.id');

        $queryItems = DB::table('items')
            ->leftJoinSub($reviewSub, 'review_summary', function($join) {
                $join->on('items.id', '=', 'review_summary.item_id');
            })
            ->leftJoinSub($itemDiscounts, 'item_discounts', function($join) {
                $join->on('items.id', '=', 'item_discounts.item_id');
            })
            ->leftJoin('categories', 'items.category_id', '=', 'categories.id')
            ->leftJoinSub($categoryDiscounts, 'category_discounts', function($join) {
                $join->on('categories.id', '=', 'category_discounts.category_id');
            })
            ->select(
                'items.id',
                'items.image',
                'items.name_en',
                'items.name_ar',
                'items.description_en',
                'items.description_ar',
                'items.price as original_price',
                DB::raw('
                    (items.price - (items.price * (
                        GREATEST(
                            IFNULL(item_discounts.item_discount_percentage, 0) / 100,
                            IFNULL(category_discounts.category_discount_percentage, 0) / 100
                        )
                    ))) as final_price
                '),
                DB::raw('IFNULL(review_summary.review_rating, 0) as review_rating'),
                DB::raw('IFNULL(review_summary.total_reviews, 0) as total_reviews')
            );

        if ($id != 0) {
            $queryItems->where('items.category_id', '=', $id);
        }

        $items = $queryItems
            ->groupBy('items.id', 'items.image', 'items.name_en', 'items.name_ar',
                    'items.description_en', 'items.description_ar', 'items.price',
                    'item_discounts.item_discount_percentage', 'category_discounts.category_discount_percentage',
                    'review_summary.review_rating', 'review_summary.total_reviews')
            ->having('review_rating', '>', 0)
            ->orderByDesc('review_rating')
            ->orderByDesc('total_reviews')
            ->take(10)
            ->get();

        return $this->api_response(true, 'Top rated item based on specific category.', $items);
    }
}
