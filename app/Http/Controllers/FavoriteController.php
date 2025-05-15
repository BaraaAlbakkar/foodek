<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FavoriteController extends Controller
{
    // Show favorites for authenticated user
    public function showFavorites()
    {
        $today = now()->toDateString();
        $clientId = Auth::id();

        $favorites = DB::table('favorites')
            ->join('items', 'favorites.item_id', '=', 'items.id')
            ->join('categories', 'items.category_id', '=', 'categories.id')
            ->leftJoin(DB::raw("(SELECT di.item_id, d.percentage
                                FROM discounts d
                                JOIN discount_items di ON d.id = di.discount_id
                                WHERE d.status IN ('active', 'new')
                                AND d.start_date <= CURDATE()
                                AND d.end_date >= CURDATE()) as item_discounts"),
                        'items.id', '=', 'item_discounts.item_id')
            ->leftJoin(DB::raw("(SELECT dc.category_id, d.percentage
                                FROM discounts d
                                JOIN discount_categories dc ON d.id = dc.discount_id
                                WHERE d.status IN ('active', 'new')
                                AND d.start_date <= CURDATE()
                                AND d.end_date >= CURDATE()) as category_discounts"),
                        'categories.id', '=', 'category_discounts.category_id')
            ->select(
                'items.id',
                'items.name_en',
                'items.name_ar',
                'items.description_en',
                'items.description_ar',
                'items.image',
                'favorites.created_at',
                'items.price as original_price',
                DB::raw('
                    items.price - (items.price * (
                        GREATEST(
                            IFNULL(item_discounts.percentage, 0),
                            IFNULL(category_discounts.percentage, 0)
                        ) / 100
                    )) as final_price
                ')
            )
            ->where('favorites.client_id', $clientId)
            ->groupBy('items.id','items.name_en',
                'items.name_ar',
                'items.description_en',
                'items.description_ar',
                'items.image',
                'favorites.created_at',
                'items.price') // مهم جداً لمنع التكرار
            ->get();

        return $this->api_response(true, $favorites->isEmpty() ? 'No favorite items found.' : 'Favorite items retrieved successfully.', $favorites);
    }

 //Add item to favorites
    public function addFavorite($itemId)
{
    if (!Auth::check()) {
        return $this->api_response(false,'Not authenticated',[],401);
    }
    /** @var \App\Models\User $user */
    $user = Auth::user();
    $item = Item::findOrFail($itemId);

    if (!$user->favorites()->where('item_id', $itemId)->exists()) {
        $user->favorites()->attach($item->id);
        return $this->api_response(true,'Item added to favorites.',[]);
    }

    return $this->api_response(false,'Item is already in your favorites.',[],400);
}

//  Remove item from favorites
    public function removeFavorite($itemId)
    {
        $user = Auth::user();
        $item = Item::findOrFail($itemId);

        /** @var \App\Models\User $user */
        if ($user->favorites()->where('item_id', $itemId)->exists()) {
            $user->favorites()->detach($itemId);
            return $this->api_response(true,'Item removed from favorites.',[]);
        }

        return $this->api_response(false,'Item is not in your favorites.',[],400);
    }
}
