<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\IngredientResource;

class IngredientController extends Controller
{
    use ApiResponseTrait;
    
    public function index()
    {
        $ingredients = IngredientResource::collection(Ingredient::get());
        return $this->apiResponse($ingredients,'success',200);
    }

    public function show($id){

        $ingredient = Ingredient::find($id);
        

        if($ingredient){
            return $this->apiResponse(new IngredientResource($ingredient),'ok',200);
        }
        return $this->apiResponse(null,'The ingredient Not Found',404);

    }

    
    public function store(Request $request){

        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'price_by_piece' => 'required|numeric|min:0',
            'image' => 'nullable|file||image|mimes:jpeg,jpg,png',
            'branch_id' => 'nullable|integer|exists:branches,id',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(null,$validator->errors(),400);
        }

        $ingredient = Ingredient::create($request->all());
        if($request->hasFile('image')){
            $image = $request->file('image');
            $filename = $request->name.'.'.$image->getClientOriginalName();
            $request->image->move(public_path('/images/ingredient'),$filename);
            $ingredient->image = $filename;
        }

        if($ingredient){
            return $this->apiResponse(new IngredientResource($ingredient),'The ingredient Save',201);
        }

        return $this->apiResponse(null,'The ingredient Not Save',400);
    }

    

    
    public function update(Request $request ,$id){

        $validator = Validator::make($request->all(), [
            'name' => 'max:255',
            'price_by_piece' => 'numeric|min:0',
            'image' => 'nullable|file||image|mimes:jpeg,jpg,png',
            'branch_id' => 'nullable|integer|exists:branches,id',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(null,$validator->errors(),400);
        }


        $ingredient =Ingredient::find($id);
        
        

        if(!$ingredient){
            return $this->apiResponse(null,'The ingredient Not Found',404);
        }

        $ingredient->update($request->all());
        if($request->hasFile('image')){
            $image = $request->file('image');
            $filename = $request->name.'.'.$image->getClientOriginalName();
            $request->image->move(public_path('/images/ingredient'),$filename);
            $ingredient->image = $filename;
        }

        if($ingredient){
            return $this->apiResponse(new IngredientResource($ingredient),'The ingredient update',201);
        }

    }

    
    public function destroy($id){

        $ingredient=Ingredient::find($id);

        if(!$ingredient){
            return $this->apiResponse(null,'The ingredient Not Found',404);
        }

        $ingredient->delete($id);

        if($ingredient){
            return $this->apiResponse(null,'The ingredient deleted',200);
        }

    }
}
