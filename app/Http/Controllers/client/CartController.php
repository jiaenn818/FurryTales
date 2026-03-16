<?php

namespace App\Http\Controllers\client;

use Illuminate\Http\Request;
use App\Models\Pet;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Voucher;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    // Show cart
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('client.login.page')->with('error', 'Please login first.');
        }

        $user = Auth::user();
        // Ensure user has a customer profile
        if (!$user->customer) {
             return redirect()->route('client.home')->with('error', 'Customer profile data missing.');
        }

        $customerID = $user->customer->customerID;

        // Get or create a cart for this customer
        $cart = Cart::firstOrCreate(['CustomerID' => $customerID]);
        $cartItems = $cart->items()->with(['pet', 'accessory', 'variant',
         'outlet'])->get();

        // For each accessory, load all available variants with stock info
        foreach ($cartItems as $item) {
            if ($item->AccessoryID && $item->OutletID) {
                $item->availableVariants = \App\Models\OutletAccessory::where('AccessoryID', $item->AccessoryID)
                    ->where('OutletID', $item->OutletID)
                    ->with('variant')
                    ->get();
            }
        }

        // Fetch all available vouchers
        $availableVouchers = \App\Models\Voucher::where(function($query) {
                $query->whereNull('expiryDate')
                      ->orWhere('expiryDate', '>', now());
            })
            ->where(function($query) {
                $query->whereNull('usageLimit')
                      ->orWhere('usageLimit', '>', 0);
            })
            ->get();

        return view('client.cart', compact('cart', 'cartItems', 'availableVouchers'));
    }

    public function add(Request $request, $id)
    {
        if (!Auth::check()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Please login first.'
            ]);
        }

        $user = Auth::user();
        if (!$user->customer) {
             return response()->json([
                'status' => 'error',
                'message' => 'Customer profile missing.'
            ]);
        }
        
        $customerID = $user->customer->customerID;
        $type = $request->input('type', 'pet'); // Default to pet for backward compatibility
        $cart = Cart::firstOrCreate(['CustomerID' => $customerID]);

        if ($type === 'accessory') {
            // Collect all detail selections from the request
            $outletID = $request->input('outlet_id');
            $variantID = $request->input('variant_id');
            $requestedQty = (int) $request->input('quantity', 1);
            if ($requestedQty < 1) $requestedQty = 1;

            $variant = \App\Models\OutletAccessory::where('VariantID', $variantID)
                ->where('OutletID', $outletID)
                ->first();

            // Check stock first
            if (!$variant || $variant->StockQty <= 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => "This product in this outlet is out of stock."
                ]);
            }
            
            // Check if requested quantity exceeds stock
            if ($requestedQty > $variant->StockQty) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Requested quantity exceeds available stock (" . $variant->StockQty . " available)."
                ]);
            }
            
            // For accessories, we check for same AccessoryID AND same OutletID AND same VariantID
            $cartItem = CartItem::where('CartID', $cart->CartID)
                ->where('AccessoryID', $id)
                ->where('OutletID', $outletID)
                ->where('VariantID', $variantID)
                ->first();

            if ($cartItem) {
                // Check if combined quantity exceeds stock
                $newTotalQty = $cartItem->Quantity + $requestedQty;
                if ($newTotalQty > $variant->StockQty) {
                    return response()->json([
                        'status' => 'info',
                        'message' => "You already have {$cartItem->Quantity} in cart. Adding {$requestedQty} more would exceed stock ({$variant->StockQty} available)."
                    ]);
                }

                $cartItem->increment('Quantity', $requestedQty);
                return response()->json([
                    'status' => 'success',
                    'message' => "Added {$requestedQty} more of {$variant->accessory->AccessoryName} to your cart!"
                ]);
            }

            CartItem::create([
                'CartID' => $cart->CartID,
                'AccessoryID' => $id,
                'OutletID' => $outletID,
                'VariantID' => $variantID,
                'Quantity' => $requestedQty
            ]);

            return response()->json([
                'status' => 'success',
                'message' => "{$requestedQty} x {$variant->accessory->AccessoryName} added to cart!"
            ]);

        } else {
            $pet = Pet::findOrFail($id);
        
            // Check if pet already exists in cart
            $cartItem = CartItem::where('CartID', $cart->CartID)
                ->where('PetID', $id)
                ->first();
        
            if ($cartItem) {
                return response()->json([
                    'status' => 'info',
                    'message' => "{$pet->PetName} is already in your cart."
                ]);
            }
        
            // Add new pet to cart
            CartItem::create([
                'CartID' => $cart->CartID,
                'PetID' => $id,
                'Quantity' => 1
            ]);
        
            return response()->json([
                'status' => 'success',
                'message' => "{$pet->PetName} added to cart!"
            ]);
        }
    }
    

    // Remove item from cart
    public function remove(Request $request, $id)
    {
        if (!Auth::check() || !Auth::user()->customer) {
             return back()->with('error', 'Please login first.');
        }

        $customerID = Auth::user()->customer->customerID;
        $cart = Cart::where('CustomerID', $customerID)->first();

        if ($cart) {
            // Find and delete the specific cart item by its primary key
            CartItem::where('CartID', $cart->CartID)
                ->where('CartItemID', $id)
                ->delete();
        }

        return back()->with('success', "Item removed from cart.");
    }

    // Update quantity
    public function updateQuantity(Request $request)
    {
        if (!Auth::check() || !Auth::user()->customer) {
            return response()->json([
                'status' => 'error',
                'message' => 'Please login first.'
            ], 401);
        }

        $cartItemId = $request->input('cart_item_id');
        $quantity = $request->input('quantity');

        $cartItem = CartItem::with(['accessory', 'variant', 'pet'])->find($cartItemId);

        if (!$cartItem) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cart item not found.'
            ], 404);
        }

        // Pets cannot have quantity changed
        if ($cartItem->PetID) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pet quantity cannot be modified.'
            ], 400);
        }

        // Validate quantity is a positive integer
        if (!is_numeric($quantity) || $quantity < 1 || $quantity != floor($quantity)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Quantity must be at least 1.'
            ], 400);
        }

        // Check stock availability for accessories
        if ($cartItem->AccessoryID && $cartItem->VariantID && $cartItem->OutletID) {
            $outletAccessory = \App\Models\OutletAccessory::where('VariantID', $cartItem->VariantID)
                ->where('OutletID', $cartItem->OutletID)
                ->first();

            if (!$outletAccessory) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Product variant not found.'
                ], 404);
            }

            if ($quantity > $outletAccessory->StockQty) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Only {$outletAccessory->StockQty} items available in stock.",
                    'max_quantity' => $outletAccessory->StockQty
                ], 400);
            }
        }

        // Update quantity
        $cartItem->Quantity = $quantity;
        $cartItem->save();

        // Calculate prices
        $unitPrice = $cartItem->variant->Price;
        $totalPrice = $unitPrice * $quantity;

        return response()->json([
            'status' => 'success',
            'message' => 'Quantity updated.',
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'total_price' => $totalPrice
        ]);
    }

    // Update variant
    public function updateVariant(Request $request)
    {
        if (!Auth::check() || !Auth::user()->customer) {
            return response()->json([
                'status' => 'error',
                'message' => 'Please login first.'
            ], 401);
        }

        $cartItemId = $request->input('cart_item_id');
        $newVariantId = $request->input('variant_id');

        $cartItem = CartItem::with(['accessory', 'outlet'])->find($cartItemId);

        if (!$cartItem) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cart item not found.'
            ], 404);
        }

        // Pets cannot have variant changed
        if ($cartItem->PetID) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pets do not have variants.'
            ], 400);
        }

        // Get the new variant and check stock
        $outletAccessory = \App\Models\OutletAccessory::where('VariantID', $newVariantId)
            ->where('OutletID', $cartItem->OutletID)
            ->where('AccessoryID', $cartItem->AccessoryID)
            ->with('variant')
            ->first();

        if (!$outletAccessory) {
            return response()->json([
                'status' => 'error',
                'message' => 'Variant not available at this outlet.'
            ], 404);
        }

        if ($outletAccessory->StockQty <= 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'This variant is out of stock.'
            ], 400);
        }

        // Update variant
        $cartItem->VariantID = $newVariantId;
        
        // Adjust quantity if it exceeds new variant's stock
        $quantityAdjusted = false;
        if ($cartItem->Quantity > $outletAccessory->StockQty) {
            $cartItem->Quantity = $outletAccessory->StockQty;
            $quantityAdjusted = true;
        }
        
        $cartItem->save();

        // Calculate new prices
        if (!$outletAccessory->variant || !isset($outletAccessory->variant->Price)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Variant price information not found.'
            ], 500);
        }

        $unitPrice = $outletAccessory->variant->Price;
        $totalPrice = $unitPrice * $cartItem->Quantity;

        return response()->json([
            'status' => 'success',
            'message' => $quantityAdjusted 
                ? "Variant updated. Quantity adjusted to {$cartItem->Quantity} (available stock)."
                : 'Variant updated successfully.',
            'quantity' => $cartItem->Quantity,
            'unit_price' => $unitPrice,
            'total_price' => $totalPrice,
            'max_stock' => $outletAccessory->StockQty,
            'quantity_adjusted' => $quantityAdjusted
        ]);
    }

    // Clear cart
    public function clear()
    {
        if (!Auth::check() || !Auth::user()->customer) {
             return back()->with('error', 'Please login first.');
        }
        $customerID = Auth::user()->customer->customerID;
        $cart = Cart::where('CustomerID', $customerID)->first();

        if ($cart) {
            $cart->items()->delete();
        }

        return back()->with('success', "Cart cleared.");
    }

    // Apply voucher
    public function applyVoucher(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['status' => 'error', 'message' => 'Please login first.'], 401);
        }

        $request->validate([
            'promo_code' => 'required|string',
            'cart_total' => 'required|numeric'
        ]);

        $promoCode = $request->input('promo_code');
        $cartTotal = $request->input('cart_total');

        $voucher = Voucher::where('voucherID', $promoCode)->first();

        if (!$voucher) {
            return response()->json(['status' => 'error', 'message' => 'Invalid voucher code.'], 404);
        }

        // 1. amount > min spend
        if ($cartTotal < $voucher->minSpend) {
            return response()->json([
                'status' => 'error', 
                'message' => 'Min spend not reached. You need RM ' . number_format($voucher->minSpend, 2) . ' to use this voucher.'
            ], 400);
        }

        // 2. date < expiry date
        if ($voucher->expiryDate && now()->gt($voucher->expiryDate)) {
            return response()->json(['status' => 'error', 'message' => 'Voucher has expired.'], 400);
        }

        // 3. number of users used < usage limit
        if ($voucher->usageLimit !== null && $voucher->usageLimit <= 0) {
            return response()->json(['status' => 'error', 'message' => 'Voucher usage limit reached.'], 400);
        }

        // Store voucher in session
        session(['applied_voucher' => [
            'voucherID' => $voucher->voucherID,
            'discountAmount' => $voucher->discountAmount
        ]]);

        return response()->json([
            'status' => 'success',
            'message' => 'Voucher applied successfully!',
            'discount' => (float)$voucher->discountAmount,
            'min_spend' => (float)$voucher->minSpend,
            'new_total' => (float)($cartTotal - $voucher->discountAmount)
        ]);
    }
}
