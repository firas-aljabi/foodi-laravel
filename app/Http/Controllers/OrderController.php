<?php

namespace App\Http\Controllers;

use App\Exports\OrdersExport;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Resources\OrderResource;
use App\Models\Ingredient;
use App\Models\OrderIngredient;
use App\Models\OrderProduct;
use App\Models\ProductIngredient;
use App\Models\Product;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;


class OrderController extends Controller
{
    use ApiResponseTrait;


    public function index()
    {
        //
        $orders = OrderResource::collection(Order::get());
        return $this->apiResponse($orders,'success',200);
    }

    public function show($id){

        $order = Order::find($id);



        if($order){
            return $this->apiResponse(new OrderResource($order),'ok',200);
        }
        return $this->apiResponse(null,'The order Not Found',404);

    }

    

public function store(Request $request)
{


    $v = $request->validate([
        'time' => 'date_format:H:i:s',
        'time_end' => 'date_format:H:i:s',
        'table_num' => 'required',
        'products' => 'required',
        'ingg' => 'nullable',
        'branch_id'=> 'exists:branches,id'
        
    ]);

    $order = new Order();
    $order->table_num = $v['table_num'];
    $order->branch_id = $v['branch_id'];
    $order->tax = 5;
    $order->time = Carbon::now()->format('H:i:s');
    $order->save();
    // Calculate the total price
    $totalPrice = 0;
    // Store the order's products
    foreach ($request->products as $productData) {
        $productId = $productData['product_id'];
        $quantity = $productData['quantity'];
        // Retrieve the product price from the database
        $product = Product::find($productId);
        $productPrice = $product->price;
        
        // Calculate the product subtotal
        $productSubtotal = $productPrice * $quantity;
        
        // Add the product subtotal to the total price
        $totalPrice += $productSubtotal;
        // Create a new order product instance
        $orderProduct = OrderProduct::create([
            'order_id' => $order->id,
            'product_id' => $productId,
            'quantity' => $quantity
        ]);
        
        //Save the order product
        $orderProduct->save();
    }
      // Store the order's ingredients
      foreach ($request->input('ingredients') as $ingredientData) {
        $ingredientId = $ingredientData['ingredient_id'];
        $quantity = $ingredientData['quantity'];
        // Retrieve the ingredient price from the database
        $ingredient = Ingredient::find($ingredientId);
        $ingredientPrice = $ingredient->price_by_piece;
        
        // Calculate the ingredient subtotal
        $ingredientSubtotal = $ingredientPrice * $quantity;
        
        // Add the ingredient subtotal to the total price
        $totalPrice += $ingredientSubtotal;
        // Create a new order ingredient instance
        $orderIngredient = new OrderIngredient();
        
        // // Set the order ingredient details
        $orderIngredient->order_id = $order->id;
        $orderIngredient->ingredient_id = $ingredientId;
        $orderIngredient->quantity = $quantity;
        
        // // Save the order ingredient
        $orderIngredient->save();
    }
        // Add the tax to the total price
        $totalPrice += ($order->tax / 100);
        
        // Set the total price of the order
        $order->total_price = $totalPrice;
        
        // Save the order
        $order->save();
    if ($order) {
        return $this->apiResponse(new OrderResource($order->load(['products'])), 'The order Save', 201);
    }
    return $this->apiResponse(null, 'The order Not Save', 400);
}



    public function update(Request $request ,$id){
        $validated = Validator::make($request->all(), [
            'status' => 'in:Preparing,Done',
            'table_num' => 'numeric|min:0',
            'products' => 'required',
            'tax' => 'nullable|numeric',
            'is_paid' => 'in:0,1',
            'total_price'=>'numeric',
        ]);

        if ($validated->fails()) {
            return $this->apiResponse(null,$validated->errors(),400);
        }


        $order=Order::find($id);
      
        if($order){
            
            $order->update($request->all());
            $order->save();
            $totalPrice = 0;
            if(isset($order->products)){
                foreach ($request->products as $productData) {
                    $productId = $productData['product_id'];
                    $quantity = $productData['quantity'];
                    
                     // Find the order product by product ID
                    $orderProduct = OrderProduct::where('order_id', $id)->where('product_id', $productId)->get();
                    foreach($orderProduct as $one){
                        // Update the quantity of the order product
                        $one->quantity = $quantity;
                        // Save the updated order product
                        $one->save();
                    }
                    $order_products = OrderProduct::where('order_id',$id)->get();
                    // return $order_products;
                    foreach($order_products as $order_product){
                        $order_product->product_id = $productData['product_id'];
                        $order_product->quantity = $productData['quantity'];
                        $order_product->save();
                    }
                       // Retrieve the product price from the database
                       $product = Product::find($productId);
                       $productPrice = $product->price;
                       
                       // Calculate the product subtotal
                       $productSubtotal = $productPrice * $quantity;
                       
                       // Add the product subtotal to the total price
                       $totalPrice += $productSubtotal;
                    
                }
               
            }if(isset($order->ingredients)){
                   // Update the order's ingredients
                   foreach ($request->input('ingredients') as $ingredientData) {
                    $ingredientId = $ingredientData['ingredient_id'];
                    $quantity = $ingredientData['quantity'];
                    
                    // Find the order ingredient by ingredient ID
                    $orderIngredient = OrderIngredient::where('order_id', $id)->where('ingredient_id', $ingredientId)->get();
                    foreach($orderIngredient as $OI){
                        // Update the quantity of the order product
                        $OI->quantity = $quantity;
                        // Save the updated order product
                        $OI->save();
                    }
                    $order_ingresients = OrderIngredient::where('order_id',$id)->get();
                    // return $order_products;
                    foreach($order_ingresients as $order_ingresient){
                        $order_ingresient->ingredient_id = $ingredientData['ingredient_id'];
                        $order_ingresient->quantity = $ingredientData['quantity'];
                        $order_ingresient->save();
                    }
                    // Retrieve the ingredient price from the database
                    $ingredient = Ingredient::find($ingredientId);
                    $ingredientPrice = $ingredient->price_by_piece;
                    
                    // Calculate the ingredient subtotal
                    $ingredientSubtotal = $ingredientPrice * $quantity;
                    
                    // Add the ingredient subtotal to the total price
                    $totalPrice += $ingredientSubtotal;
                }
            }
            
             // Add the tax to the total price
            $totalPrice += ($order->tax /100);
            
            // Set the total price of the order
            $order->total_price = $totalPrice;
            
            // Save the order
            $order->save();
            
            return $this->apiResponse(new OrderResource($order->load(['products'])), 'The order saved', 201);
        }else{
            return $this->apiResponse(null,'The order not found',404);
        } 
   
}

    public function destroy($id){

        $order=Order::find($id);

        if(!$order){
            return $this->apiResponse(null,'The order Not Found',404);
        }

        $order->delete($id);

        if($order){
            return $this->apiResponse(null,'The order deleted',200);
        }

    }

    public function peakTimes()
   {
   
    $peakHours = Order::select('time')->groupBy('time')->orderByRaw('COUNT(time) DESC')->first();
    if ($peakHours) {
        return $this->apiResponse($peakHours,'success',200);
    } else {
        return $this->apiResponse(null,'No product has been requested yet',404);
    }
    
   }

    public function report_order(Request $request)
    {
        $start_at = date($request->start_at);
        $end_at = date($request->end_at);
        $orders = Order::whereBetween('created_at', [$start_at,$end_at])->get();
        if ($orders) {
            return $this->apiResponse($orders,'success',200);
        } else {
            return $this->apiResponse(null,'Not Found',404);
        }
    }
    public function export(Request $request) 
    {
        $start_at = date($request->start_at);
        $end_at = date($request->end_at);
        return Excel::download(new OrdersExport($start_at,$end_at), 'orders.xlsx');
    }
    public function readyOrder($id){

        $order = Order::where('id', $id)->first();
        $start_at = Carbon::parse($order->time);
        $end_at = Carbon::parse($order->time_end);
        $preparationTime = $end_at->diff($start_at)->format('%H:%i:%s');

        if($preparationTime){
            return $this->apiResponse($preparationTime,'success',200);
        } else {
            return $this->apiResponse(null,'Not Found',404);
        }

    }
}

