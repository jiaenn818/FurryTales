<?php

namespace App\Http\Controllers\client;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Payment;
use App\Models\Pet;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Voucher;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function checkout(Request $request)
    {
        $request->validate([
            'cart_item_ids' => 'required|array',
            'delivery_method' => 'required|in:pickup,delivery',
            'delivery_address' => 'required_if:delivery_method,delivery',
        ]);

        $cartItemIDs = $request->input('cart_item_ids');
        $deliveryMethod = $request->input('delivery_method');
        $contactName = $request->input('contact_name');
        $contactPhone = $request->input('contact_phone');
        $deliveryAddressLine = $request->input('delivery_address');
        $postcode = $request->input('delivery_postcode');
        $state = $request->input('delivery_state');
        $pickupTime = $request->input('pickup_time');

        // Construct Final Address / Info string
        $finalAddress = "";
        if ($deliveryMethod === 'delivery') {
            $finalAddress = "{$deliveryAddressLine}, {$state} | Receiver: {$contactName} ({$contactPhone})";
            $pickupTime = null;
        } else {
            $finalAddress = null;
            $postcode = null;
            $state = null;
        }
        
        // Fetch cart items with products and variants
        $cartItems = CartItem::with(['pet', 'accessory', 'variant'])->whereIn('CartItemID', $cartItemIDs)->get();

        if ($cartItems->isEmpty()) {
            return back()->with('error', 'No valid items selected for checkout.');
        }

        $lineItems = [];
        $totalAmount = 0;

        foreach ($cartItems as $item) {
            $isPet = !empty($item->PetID);
            $product = $isPet ? $item->pet : $item->accessory;
            $name = $isPet ? $product->PetName : $product->AccessoryName;
            $description = $isPet ? $product->Breed : $product->Brand;

            // Calculate correct price
            if ($isPet) {
                $unitPrice = $product->Price;
            } else {
                // For accessories, get price from the variant
                $unitPrice = $item->variant ? $item->variant->Price : $product->Price;
            }

            if (!$isPet && $item->SelectedDetails) {
                $detailsStr = collect($item->SelectedDetails)->map(fn($v, $k) => "$k: $v")->implode(', ');
                $description .= " ($detailsStr)";
            }

            $lineItems[] = [
                'price_data' => [
                    'currency' => 'myr',
                    'product_data' => [
                        'name' => $name,
                        'description' => $description,
                    ],
                    'unit_amount' => (int) ($unitPrice * 100),
                ],
                'quantity' => $item->Quantity,
            ];
            $totalAmount += ($unitPrice * $item->Quantity);
        }

        // Apply Voucher Discount using Stripe Coupons
        $appliedVoucher = session('applied_voucher');
        $discountAmount = 0;
        $voucherID = null;
        $stripeDiscounts = [];

        Stripe::setApiKey(env('STRIPE_SECRET'));

        if ($appliedVoucher) {
            $voucher = Voucher::where('voucherID', $appliedVoucher['voucherID'])->first();
            if ($voucher && 
                ($voucher->usageLimit === null || $voucher->usageLimit > 0) && 
                (!$voucher->expiryDate || now()->lt($voucher->expiryDate)) &&
                ($totalAmount >= $voucher->minSpend)) {
                
                $discountAmount = (float)$voucher->discountAmount;
                $voucherID = $voucher->voucherID;

                // Create a one-time Stripe Coupon
                $coupon = \Stripe\Coupon::create([
                    'amount_off' => (int) ($discountAmount * 100),
                    'currency' => 'myr',
                    'duration' => 'once',
                    'name' => 'Voucher: ' . $voucherID,
                ]);

                $stripeDiscounts[] = ['coupon' => $coupon->id];
            } else {
                // Clear invalid/expired voucher from session
                session()->forget('applied_voucher');
            }
        }

        $checkoutSession = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'mode' => 'payment',
            'discounts' => $stripeDiscounts,
            'success_url' => route('client.payment.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('client.payment.cancel'),
            'metadata' => [
                'customer_id' => Auth::user()->customer->customerID,
                'cart_item_ids' => implode(',', $cartItemIDs),
                'delivery_method' => $deliveryMethod,
                'delivery_address' => $finalAddress,
                'postcode' => $postcode,
                'state' => (string) ($state ?? ''),
                'time' => (string) ($pickupTime ?? ''),
                'voucher_id' => (string) ($voucherID ?? ''),
                'discount_amount' => (string) $discountAmount,
            ],
        ]);

        return redirect($checkoutSession->url);
    }

    public function success(Request $request)
    {
        $sessionId = $request->get('session_id');

        if (!$sessionId) {
            \Log::error('Payment Success: No session ID');
            return redirect()->route('client.cart.view')->with('error', 'Invalid session.');
        }

        Stripe::setApiKey(env('STRIPE_SECRET'));
        
        try {
            $session = Session::retrieve($sessionId);
        } catch (\Exception $e) {
            \Log::error('Payment Success: Stripe Retrieve Error: ' . $e->getMessage());
            return redirect()->route('client.cart.view')->with('error', 'Payment verification failed.');
        }

        if ($session->payment_status !== 'paid') {
            \Log::error('Payment Success: Payment status not paid: ' . $session->payment_status);
            return redirect()->route('client.cart.view')->with('error', 'Payment not completed.');
        }

        // Process Order
        $metadata = $session->metadata;
        $cartItemIDs = explode(',', $metadata->cart_item_ids);
        $customerID = $metadata->customer_id; 
        
        $customer = DB::table('customers')->where('customerID', $customerID)->first();
        
        if (!$customer) {
             \Log::error('Payment Success: Customer profile not found for CustomerID: ' . $customerID);
             return redirect()->route('client.home')->with('error', 'Customer profile not found.');
        }

        DB::beginTransaction();
        try {
            // 1. Create Purchase
            $purchaseID = Str::random(10);
            
            \Log::info('Payment Success: Creating Purchase: ' . $purchaseID);

            $purchase = Purchase::create([
                'PurchaseID' => $purchaseID,
                'CustomerID' => $customer->customerID,
                'OrderDate' => now(),
                'Method' => ucfirst($metadata->delivery_method),
                'TotalAmount' => $session->amount_total / 100,
                'VoucherID' => !empty($metadata->voucher_id) ? $metadata->voucher_id : null,
                'DiscountAmount' => !empty($metadata->discount_amount) ? (float)$metadata->discount_amount : 0.00,
                'DeliveryAddress' => $metadata->delivery_address,
                'Postcode' => $metadata->postcode ?? null,
                'State' => $metadata->state ?? null,
                'Time' => $metadata->time ?? null,
                'Status' => 'Pending',
                'DeliveredDate' => null,
            ]);

            // 2. Fetch cart items to create PurchaseItems
            $cartItems = CartItem::with(['pet', 'accessory', 'variant'])->whereIn('CartItemID', $cartItemIDs)->get();

            foreach ($cartItems as $item) {
                $isPet = !empty($item->PetID);
                $product = $isPet ? $item->pet : $item->accessory;

                // Calculate correct price
                if ($isPet) {
                    $itemPrice = $product->Price;
                } else {
                    // For accessories, get price from the variant
                    $itemPrice = $item->variant ? $item->variant->Price : $product->Price;
                }

                PurchaseItem::create([
                    'PurchaseID' => $purchaseID,
                    'ItemID' => $isPet ? $item->PetID : null,
                    'AccessoryID' => !$isPet ? $item->AccessoryID : null,
                    'OutletID' => !$isPet ? $item->OutletID : null,
                    'VariantID' => !$isPet ? $item->VariantID : null,
                    'Quantity' => $item->Quantity,
                    'Price' => $itemPrice,
                    'SelectedDetails' => $item->SelectedDetails
                ]);

                // Update stock if it's an accessory
                if (!$isPet && $item->AccessoryID && $item->OutletID) {
                    $stockQuery = DB::table('outlet_accessories')
                        ->where('AccessoryID', $item->AccessoryID)
                        ->where('OutletID', $item->OutletID);
                    
                    if ($item->VariantID) {
                        $stockQuery->where('VariantID', $item->VariantID);
                    } else {
                        $stockQuery->whereNull('VariantID');
                    }
                    
                    $stockQuery->decrement('StockQty', $item->Quantity);
                    
                    \Log::info("Stock Deducted: Accessory {$item->AccessoryID}, Variant {$item->VariantID}, Outlet {$item->OutletID}, Qty {$item->Quantity}");
                }

                // Update pet status if it's a pet
                if ($isPet) {
                    // Assuming there's some logic to mark pet as sold, but let's stick to existing logic
                }
            }

            // Update Voucher Usage
            if (!empty($metadata->voucher_id)) {
                Voucher::where('voucherID', $metadata->voucher_id)->decrement('usageLimit');
                session()->forget('applied_voucher');
                \Log::info("Voucher Used: {$metadata->voucher_id}, Usage Limit Decremented");
            }

            // 3. Create Payment
            Payment::create([
                'PurchaseID' => $purchaseID,
                'PaymentMethod' => 'Stripe',
                'Amount' => $session->amount_total / 100,
                'PaymentStatus' => 'Success',
                'TransactionID' => $session->payment_intent,
            ]);

            // 4. Remove from Cart
            CartItem::whereIn('CartItemID', $cartItemIDs)->delete();

            DB::commit();
            \Log::info('Payment Success: Transaction Committed');

            return redirect()->route('client.payment.complete')->with('success', 'Payment successful! Order #' . $purchaseID . ' created.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Payment Success: Transaction Failed: ' . $e->getMessage());
            return redirect()->route('client.cart.view')->with('error', 'Error creating order: ' . $e->getMessage());
        }
    }

    public function cancel()
    {
        return redirect()->route('client.cart.view')->with('info', 'Payment cancelled.');
    }
}
