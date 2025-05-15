<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;
use Symfony\Component\CssSelector\Node\FunctionNode;
use App\Models\Item;
use App\Models\Option;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Container\Attributes\Log;
use Illuminate\Contracts\Cache\Lock;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log as FacadesLog;

class CartController extends Controller
{

    public function getCartItems()
    {
        $userId = Auth::id();
        $now = now();

        $cartItems = Cart::with([
                'item.category',
                'options',
                'item.itemDiscounts',
                'item.category.categoryDiscounts'
            ])
            ->where('client_id', $userId)
            ->whereNull('deleted_at')
            ->get()
            ->map(function ($cart) use ($now) {
                $item = $cart->item;
                $category = $item->category;

                // Get highest applicable item discount
                $itemDiscount = $item->itemDiscounts
                    ->whereIn('status', ['new', 'active'])
                    ->where('start_date', '<=', $now)
                    ->where('end_date', '>=', $now)
                    ->sortByDesc('percentage')
                    ->first();

                // Get highest applicable category discount
                $categoryDiscount = optional($category)?->categoryDiscounts
                    ->whereIn('status', ['new', 'active'])
                    ->where('start_date', '<=', $now)
                    ->where('end_date', '>=', $now)
                    ->sortByDesc('percentage')
                    ->first();

                $discountPercent = max(
                    $itemDiscount->percentage ?? 0,
                    $categoryDiscount->percentage ?? 0
                );

                // Calculate discounted base price
                $basePrice = $item->price;
                $discountedPrice = $basePrice - ($basePrice * ($discountPercent / 100));
                $discountedPrice = max($discountedPrice, 0.01);

                // Add options total if any
                $optionsTotal = $cart->options->sum('extra_price');

                $finalUnitPrice = $discountedPrice + $optionsTotal;

                return [
                    'id' => $cart->id,
                    'item_name_en' => $item->name_en,
                    'item_name_ar' => $item->name_ar,
                    'description_en' => $item->description_en,
                    'description_ar' => $item->description_ar,
                    'quantity' => $cart->quantity,
                    'price' => round($finalUnitPrice, 2), // unit price only
                    'options' => $cart->options->map(function ($option) {
                        return [
                            'id' => $option->id,
                            'name_en' => $option->name_en,
                            'name_ar' => $option->name_ar,
                            'extra_price' => $option->extra_price,
                            'range' => $option->pivot->range, // from pivot (nullable)
                        ];
                    }),
                ];
            });

        if ($cartItems->isEmpty()) {
            return $this->api_response(false,'No items in the cart',[],204);
        }

        return $this->api_response(true,'Cart items retrieved successfully.',$cartItems);
    }


    // public function addToCart(Request $request){
    //     $validated = $request->validate([
    //         'user_id' => 'required|exists:users,id',
    //         'item_id' => 'required|exists:items,id',
    //         'quantity' => 'required|integer|min:1',
    //         'option_ids' => 'array',
    //         'option_ids.*' => 'exists:options,id',
    //     ]);

    //     $user = User::find($validated['user_id']);
    //     $item = Item::with('category')->find($validated['item_id']);
    //     $quantity = $validated['quantity'];
    //     $option_ids = $validated['option_ids'] ?? [];

    //     // Handle options if any
    //     $options = collect();
    //     $options_total = 0;

    //     if (!empty($option_ids)) {
    //         $options = Option::whereIn('id', $option_ids)->get();
    //         $options_total = $options->sum('extra_price');
    //     }

    //     // Check for item/category discount
    //     $now = now();

    //     $itemDiscount = $item->itemDiscounts()
    //         ->whereIn('status', ['new', 'active'])
    //         ->whereDate('start_date', '<=', $now)
    //         ->whereDate('end_date', '>=', $now)
    //         ->orderByDesc('percentage')
    //         ->first();

    //     $categoryDiscount = $item->category?->categoryDiscounts()
    //         ->whereIn('status', ['new', 'active'])
    //         ->whereDate('start_date', '<=', $now)
    //         ->whereDate('end_date', '>=', $now)
    //         ->orderByDesc('percentage')
    //         ->first();

    //     // Determine the highest valid discount
    //     $discountPercent = max(
    //         $itemDiscount->percentage ?? 0,
    //         $categoryDiscount->percentage ?? 0
    //     );

    //     // Apply discount to base item price (not options)
    //     $base_price = $item->price;
    //     $discounted_price = $base_price - ($base_price * ($discountPercent / 100));
    //     $discounted_price = max($discounted_price, 0.01); // Prevent 0 or negative prices

    //     // Final unit price (item + options, no quantity)
    //     $unit_price = $discounted_price + $options_total;

    //     // Check for duplicate with same options
    //     $duplicate = Cart::where('client_id', $user->id)
    //         ->where('item_id', $item->id)
    //         ->get()
    //         ->first(function ($cart) use ($option_ids) {
    //             $cartOptionIds = $cart->options->pluck('id')->sort()->values()->toArray();
    //             return $cartOptionIds === collect($option_ids)->sort()->values()->toArray();
    //         });

    //     if ($duplicate) {
    //         return $this->api_response(false,'Item with selected options already in cart',['data' => $duplicate->load('options')],404);
    //     }

    //     // Create cart item
    //     $cartItem = Cart::create([
    //         'client_id' => $user->id,
    //         'item_id' => $item->id,
    //         'quantity' => $quantity,
    //         'total_price' => $unit_price, // not multiplied by quantity
    //     ]);

    //     // Attach options if any
    //     foreach ($options as $option) {
    //         $cartItem->options()->attach($option->id, [
    //             'price' => $option->extra_price,
    //             'range' => null,
    //         ]);
    //     }

    //     return $this->api_response(true,'Item added to cart successfully',['data' => $cartItem->load('options')]);

    // }


    public function history(){
        $orders = DB::table('orders')
            ->join('addresses','addresses.order_id','orders.id')
            ->where('orders.client_id','=',Auth::id())
            ->where('addresses.client_id','=',Auth::id())
            ->where('orders.status','Complete')
            ->select(
                'orders.id',
                'orders.total_price',
                'orders.updated_at',
                'addresses.*'
            )
            ->get();

        if($orders->isEmpty()){
            return $this->api_response(false,'There are no orders before',[],204);
        }
        return $this->api_response(true,'completed orders returns successfully',$orders);
    }

public function reorder($orderId, $itemId = null)
{
    // Get items from the completed order
    $items = DB::table('orders')
        ->join('order_items', 'orders.id', '=', 'order_items.order_id')
        ->join('items', 'items.id', '=', 'order_items.item_id')
        ->select(
            'items.id as item_id',
            'items.name_en',
            'items.is_active',
            'items.price as base_price',
            'order_items.quantity',
            'order_items.id as order_item_id'
        )
        ->where('orders.client_id', Auth::id())
        ->where('orders.id', $orderId)
        ->where('orders.status', 'Complete')
        ->when($itemId, fn($query) => $query->where('items.id', $itemId))
        ->get();

    if ($items->isEmpty()) {
        return $this->api_response(false, 'No items found in this order', [], 404);
    }

    foreach ($items as $item) {
        if (!$item->is_active) {
            return $this->api_response(false, 'Item is not active', ['item name' => $item->name_en]);
        }

        // Check if item already exists in cart
        $alreadyInCart = DB::table('carts')
            ->where('client_id', Auth::id())
            ->where('item_id', $item->item_id)
            ->whereNull('deleted_at') // in case using soft deletes
            ->exists();

        if ($alreadyInCart) {
            return $this->api_response(false, 'Item already exists in cart', ['item name' => $item->name_en], 409);
        }

        // Get options for this order_item
        $options = DB::table('order_options')
            ->join('options', 'order_options.option_id', '=', 'options.id')
            ->where('order_options.order_item_id', $item->order_item_id)
            ->select('options.id as option_id', 'options.extra_price')
            ->get();

        // Calculate total price (base + options)
        $optionTotal = $options->sum('extra_price');
        $totalPrice = ($item->base_price + $optionTotal) * $item->quantity;

        // Insert into cart
        $cartId = DB::table('carts')->insertGetId([
            'client_id' => Auth::id(),
            'item_id' => $item->item_id,
            'quantity' => $item->quantity,
            'total_price' => $totalPrice,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Insert options into cart_option
        foreach ($options as $opt) {
            DB::table('cart_option')->insert([
                'cart_id' => $cartId,
                'option_id' => $opt->option_id,
                'price' => $opt->extra_price,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    return $this->api_response(true, 'Order items added to cart successfully');
}

public function historyDetailsWithOptions($orderId)
{
    $orderItems = DB::table('orders')
        ->join('order_items', 'orders.id', '=', 'order_items.order_id')
        ->join('items', 'items.id', '=', 'order_items.item_id')
        ->select(
            'order_items.id as order_item_id',
            'items.id as item_id',
            'items.name_en',
            'items.name_ar',
            'items.image',
            'order_items.quantity',
            'order_items.price'
        )
        ->where('orders.client_id', Auth::id())
        ->where('orders.id', $orderId)
        ->where('orders.status', 'Complete')
        ->get();

    if ($orderItems->isEmpty()) {
        return $this->api_response(false, 'No items found in this order', [], 204);
    }

    // Loop and attach options to each item
    $itemsWithOptions = $orderItems->map(function ($item) {
        $options = DB::table('order_options')
            ->join('options', 'order_options.option_id', '=', 'options.id')
            ->where('order_options.order_item_id', $item->order_item_id)
            ->select(
                'options.id',
                'options.name_en',
                'options.name_ar',
                'order_options.range'
            )
            ->get();

        $item->options = $options;
        return $item;
    });

    return $this->api_response(true, 'Order items with options returned successfully', $itemsWithOptions);
}

public function deleteCartItem($cartId){
    $cartItem = Cart::find($cartId);

    if (!$cartItem) {
        return $this->api_response(false,'Cart item not found.',[],404);
    }

    $cartItem->delete(); // uses soft delete

    return $this->api_response(true,'Cart item deleted successfully.',[]);
}

public function reduceQuantityByOne(Request $request){

    $clientId = Auth::id();
    $itemId = $request->item_id;



    $cartItem = Cart::where('client_id', $clientId)
        ->where('item_id', $itemId)
        ->whereNull('deleted_at')
        ->first();

    if (!$cartItem) {
        return $this->api_response(false,'Cart item not found.',[],404);
    }

    if ($cartItem->quantity > 1) {
        $cartItem->quantity -= 1;
        $cartItem->save();

        return $this->api_response(true,'One piece removed from cart.',[]);
    }

    return $this->api_response(false,'Minimum quantity is 1. Cannot reduce further.',[],404);
}


public function increaseQuantityByOne(Request $request)
{
    $clientId = Auth::id();
    $itemId = $request->item_id;

    if (!$clientId || !$itemId) {
        return $this->api_response(false,'Both client_id and item_id are required.',[],400);
    }

    $cartItem = Cart::where('client_id', $clientId)
        ->where('item_id', $itemId)
        ->whereNull('deleted_at')
        ->first();

    if (!$cartItem) {
        return $this->api_response(false,'Cart item not found.',[],404);
    }

    if ($cartItem->quantity >= 50) {
        return $this->api_response(false,'Maximum quantity of 50 reached. Cannot add more.',['quantity' => $cartItem->quantity],400);
    }

    $cartItem->quantity += 1;
    $cartItem->save();

    return $this->api_response(true,'One piece added to cart.',['quantity' => $cartItem->quantity]);
}

public function addToCart(Request $request)
{
    $validated = $request->validate([
        'item_id' => 'required|exists:items,id',
        'quantity' => 'required|integer|min:1',
        'options' => 'nullable|array',
        'options.*.id' => 'required|exists:options,id',
        'options.*.range' => 'nullable|string',
    ]);

    $user = Auth::user();
    if (!$user) {
        return $this->api_response(false,'Unauthenticated user.',[],401);
    }

    $item = Item::with(['category.categoryDiscounts', 'itemDiscounts'])->find($validated['item_id']);
    if (!$item) {
        return $this->api_response(false,'Item not found.',[],404);
    }

    $quantity = $validated['quantity'];
    $optionsData = $validated['options'] ?? [];

    $option_ids = collect($optionsData)->pluck('id')->toArray();
    $options = Option::whereIn('id', $option_ids)->get();

    $options_total = $options->sum('extra_price');
    $now = now();

    $itemDiscount = $item->itemDiscounts
        ->whereIn('status', ['new', 'active'])
        ->where('start_date', '<=', $now)
        ->where('end_date', '>=', $now)
        ->sortByDesc('percentage')
        ->first();

    $categoryDiscount = optional($item->category)?->categoryDiscounts
        ->whereIn('status', ['new', 'active'])
        ->where('start_date', '<=', $now)
        ->where('end_date', '>=', $now)
        ->sortByDesc('percentage')
        ->first();

    $discountPercent = max(
        $itemDiscount->percentage ?? 0,
        $categoryDiscount->percentage ?? 0
    );

    $base_price = $item->price;
    $discounted_price = max($base_price - ($base_price * ($discountPercent / 100)), 0.01);
    $unit_price = $discounted_price + $options_total;

    // Check for duplicate item in cart
    $duplicate = Cart::with('options')->where('client_id', $user->id)
        ->where('item_id', $item->id)
        ->get()
        ->first(function ($cart) use ($option_ids) {
            $cartOptionIds = $cart->options->pluck('id')->sort()->values()->toArray();
            return $cartOptionIds === collect($option_ids)->sort()->values()->toArray();
        });

    if ($duplicate) {
        // Transform options to move range from pivot and remove pivot
        $duplicate->options->transform(function ($option) {
            $option->range = $option->pivot->range;
            unset($option->pivot);
            return $option;
        });

        return $this->api_response(false,'Item with selected options already in cart',[],404);
    }

    $cartItem = Cart::create([
        'client_id' => $user->id,
        'item_id' => $item->id,
        'quantity' => $quantity,
        'total_price' => $unit_price,
    ]);

    foreach ($options as $option) {
        $matchingOption = collect($optionsData)->firstWhere('id', $option->id);
        $range = $matchingOption['range'] ?? null;

        $cartItem->options()->attach($option->id, [
            'price' => $option->extra_price,
            'range' => $range,
        ]);
    }
    $cartItem->unit_price = number_format($unit_price, 2);
    $cartItem->load('options');

    // Transform options to move range from pivot and remove pivot
    $cartItem->options->transform(function ($option) {
        $option->range = $option->pivot->range;
        unset($option->pivot);
        return $option;
    });

    return $this->api_response(true,'Item added to cart successfully',$cartItem);
}

}
