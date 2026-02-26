<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AddToCartRequest;
use App\Http\Requests\Api\RemoveFromCartRequest;
use App\Http\Resources\CartItemResource;
use App\Http\Resources\CartResource;
use App\Http\Resources\ChildProductResource;
use App\Models\OrderItem;
use App\Models\Product;
use App\Traits\BaseApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

/**
 * @group Api
 * @subgroup Cart
 */
class CartController extends Controller
{
    use BaseApiResponseTrait;

    /**
     * Add item to cart or increase quantity if exists.
     * @authenticated
     * @bodyParam product_id integer required The ID of the product.
     * @bodyParam quantity integer optional The quantity to add (default: 1).
     * @return JsonResponse
     */
    public function addToCart(AddToCartRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $user = auth('sanctum')->user();
            $productId = $request->product_id;
            $quantity = $request->quantity ?? 1;
            $product = Product::findOrFail($productId);
            $price = $product->price;

            $cartItem = OrderItem::where('user_id', $user->id)
                ->whereNull('order_id')
                ->where('child_product_id', $productId)
                ->where('product_id', $product->parent_id)
                ->first();

            if ($cartItem) {
                $newQuantity = $cartItem->quantity + $quantity;
                if ($product->quantity < $newQuantity) {
                    DB::rollBack();
                    return $this->errorWrongArgs(__('trans.insufficient_quantity'));
                }
                $cartItem->quantity = $newQuantity;
                // $cartItem->total_price = $cartItem->quantity * $cartItem->price;

                $cartItem->save();
            } else {
                if ($product->quantity < $quantity) {
                    DB::rollBack();
                    return $this->errorWrongArgs(__('trans.insufficient_quantity'));
                }
                $cartItem = OrderItem::create([
                    'user_id' => $user->id,
                    'order_id' => null,
                    'child_product_id' => $productId,
                    'product_id' => $product->parent_id,
                    'quantity' => $quantity,
                    // 'price' => $price,
                    // 'total_price' => $quantity * $price,
                    'note' => $request->note,
                ]);
            }

            DB::commit();

            $cartItem->load(['product', 'childProduct']);

            return $this->respondWithSuccess(
                __('trans.item_added_to_cart'),
                [
                    'cart_item' => new CartItemResource($cartItem)
                ]
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->respondWithError($e->getMessage());
        }
    }

    /**
     * Remove item from cart or reduce quantity.
     * @authenticated
     * @bodyParam cart_item_id integer required The ID of the cart item.
     * @bodyParam quantity integer optional The quantity to remove (default: 1). If quantity becomes 0 or less, item is removed.
     * @return JsonResponse
     */
    public function removeFromCart(RemoveFromCartRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $user = auth('sanctum')->user();
            $cartItem = OrderItem::where('id', $request->cart_item_id)
                ->where('user_id', $user->id)
                ->whereNull('order_id')
                ->firstOrFail();

            $quantityToRemove = $request->quantity ?? 1;

            if ($cartItem->quantity <= $quantityToRemove) {
                $cartItem->forceDelete();
                $message = __('trans.item_removed_from_cart');
            } else {
                // Reduce quantity
                $cartItem->quantity -= $quantityToRemove;
                $cartItem->total_price = $cartItem->quantity * $cartItem->price;
                $cartItem->save();
                $message = __('trans.quantity_reduced');
            }

            DB::commit();

            return $this->respondWithSuccess($message);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->respondWithError($e->getMessage());
        }
    }

    /**
     * Empty cart - remove all items from user's cart.
     * @authenticated   
     * @return JsonResponse
     */
    public function emptyCart(): JsonResponse
    {
        try {
            $user = auth('sanctum')->user();

            $deletedCount = OrderItem::where('user_id', $user->id)
                ->whereNull('order_id')
                ->forceDelete();

            return $this->respondWithSuccess(
                __('trans.cart_emptied'),
                ['deleted_items' => $deletedCount]
            );
        } catch (\Exception $e) {
            return $this->respondWithError($e->getMessage());
        }
    }

    /**
     * Get cart items.
     * @authenticated
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {


        try {
            $user = auth('sanctum')->user();

            $cartItems = OrderItem::cartItems($user->id)->with(['product', 'childProduct'])->get();

            $total = $cartItems->sum(function ($item) {
                return ($item->childProduct->discounted_price ?? $item->childProduct->price) * $item->quantity;
            });
            dd('here');
            $total_original_price = $cartItems->sum(function ($item) {
                return $item->childProduct->price * $item->quantity;
            });

            $total_discount = $total_original_price - $total;



            return $this->respondWithSuccess(
                __('trans.cart_retrieved'),
                [
                    'items' => CartItemResource::collection($cartItems),
                    'items_count' => $cartItems->count(),
                    'total_price_original' => $total_original_price,
                    'total_price_discounted' => $total,
                    'total_discount' => round($total_discount, 2),
                ]
            );
        } catch (\Exception $e) {
            return $this->respondWithError($e->getMessage());
        }
    }
}
