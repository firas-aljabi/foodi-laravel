<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\ProductResource;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\ProductIngredient;
use App\Models\Rating;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller

{
    use ApiResponseTrait;
    
    public function index()
    {
        
        $products = ProductResource::collection(Product::get());
        return $this->apiResponse($products,'success',200);
    }

    public function show($id){

        $product = Product::find($id);
        

        if($product){
            return $this->apiResponse(new ProductResource($product),'ok',200);
        }
        return $this->apiResponse(null,'The product Not Found',404);

    }

    
    public function store(Request $request){

        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string|min:3|max:2500',
            'image' => 'nullable|file|image|mimes:jpeg,jpg,png',
            'category_id' => 'nullable|integer|exists:categories,id',
            'branch_id' => 'nullable|integer|exists:branches,id',
            'ingredients' => 'required|string|min:3|max:2500',
            'estimated_time'=>'nullable'
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(null,$validator->errors(),400);
        }

        $product = new Product();
        $product->name = $request->name;
        $product->description = $request->description;
        $product->price = $request->price;
        $product->ingredients = $request->ingredients;
        $product->notes = $request->notes;
        $product->category_id = $request->category_id;
        $product->branch_id = $request->branch_id;
        if($request->hasFile('image')){
            $image = $request->file('image');
            $filename = $request->name.'.'.$image->getClientOriginalName();
            $request->image->move(public_path('/images/product'),$filename);
            $product->image = $filename;
        }
        $product->save();
        

        if($product){
            return $this->apiResponse(new ProductResource($product),'The product Save',201);
        }

        return $this->apiResponse(null,'The product Not Save',400);
    }

    

    
    public function update(Request $request ,$id){

        $validator = Validator::make($request->all(), [
            'name' => 'max:255',
            'price' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|min:3|max:2500',
            'image' => 'nullable|file||image|mimes:jpeg,jpg,png',
            'category_id' => 'integer|exists:categories,id',
            'branch_id' => 'nullable|integer|exists:branches,id',
            'estimated_time'=>'nullable'
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(null,$validator->errors(),400);
        }


        $product=Product::find($id);
        if($product){
            $product->update($request->all());
            if($request->hasFile('image')){
                $image = $request->file('image');
                $filename = $request->name.'.'.$image->getClientOriginalName();
                $request->image->move(public_path('/images/product'),$filename);
                $product->image = $filename;
            }
            $product->save();
            return $this->apiResponse(new ProductResource($product),'The product update',201);
        }else{
            return $this->apiResponse(null,'The product Not Found',404);
        }

    }

    
    public function destroy($id){

        $product=Product::find($id);

        if(!$product){
            return $this->apiResponse(null,'The product Not Found',404);
        }

        $product->delete($id);

        if($product){
            return $this->apiResponse(null,'The product deleted',200);
        }

    }

    public function mostRequestedProduct(){

       $mostRequestedProduct = OrderProduct::select('product_id')
       ->groupBy('product_id')
       ->orderByRaw('COUNT(product_id) DESC')
       ->limit(5)
       ->get();
        if ($mostRequestedProduct) {
            return $this->apiResponse($mostRequestedProduct,'success',200);
        } else {
            return $this->apiResponse(null,'No product has been requested yet',404);
        }
    }

    public function leastRequestedProduct(){

        $leastRequestedProduct = OrderProduct::select('product_id')
        ->groupBy('product_id')
        ->orderByRaw('COUNT(product_id)')
        ->limit(5)
        ->get();
         if ($leastRequestedProduct) {
             return $this->apiResponse($leastRequestedProduct,'success',200);
         } else {
             return $this->apiResponse(null,'No product has been requested yet',404);
         }
     }


}
