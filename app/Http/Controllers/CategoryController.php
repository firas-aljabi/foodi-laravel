<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\CategoryResource;

class CategoryController extends Controller
{
    use ApiResponseTrait;
    
    public function index()
    {
        
        $products = CategoryResource::collection(Category::get());
        return $this->apiResponse($products,'success',200);
    }

    public function show($id){

        $category = Category::find($id);

        if($category){
            return $this->apiResponse(new CategoryResource($category),'ok',200);
        }
        return $this->apiResponse(null,'The Category Not Found',404);

    }

    
    public function store(Request $request){

        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'position' => 'nullable',
            'image' => 'nullable|file||image|mimes:jpeg,jpg,png',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(null,$validator->errors(),400);
        }

        $category = Category::create($request->all());
        if($request->hasFile('image')){
            $image = $request->file('image');
            $filename = $request->name.'.'.$image->getClientOriginalName();
            $request->image->move(public_path('/images/category'),$filename);
            $category->image = $filename;
        }

        if($category){
            return $this->apiResponse(new CategoryResource($category),'The Category Save',201);
        }

        return $this->apiResponse(null,'The Category Not Save',400);
    }

    

    
    public function update(Request $request ,$id){

        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'position' => 'nullable',
            'image' => 'nullable|file||image|mimes:jpeg,jpg,png',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(null,$validator->errors(),400);
        }

        $category=Category::find($id);

        if(!$category){
            return $this->apiResponse(null,'The Category Not Found',404);
        }

        $category->update($request->all());
        if($request->hasFile('image')){
            $image = $request->file('image');
            $filename = $request->name.'.'.$image->getClientOriginalName();
            $request->image->move(public_path('/images/category'),$filename);
            $category->image = $filename;
        }
        if($category){
            return $this->apiResponse(new CategoryResource($category),'The Category update',201);
        }

    }

    
    public function destroy($id){

        $category=Category::find($id);

        if(!$category){
            return $this->apiResponse(null,'The Category Not Found',404);
        }

        $category->delete($id);

        if($category){
            return $this->apiResponse(null,'The Category deleted',200);
        }

    }
}
