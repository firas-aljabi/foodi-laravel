<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\BranchResource;
use App\Models\Branch;

class BranchController extends Controller
{
    use ApiResponseTrait;
    
    public function index(Request $request)
    {
        
        $branches = BranchResource::collection(Branch::get());
        return $this->apiResponse($branches,'success',200);
    }

    public function show($id){

        $branch = Branch::find($id);
        

        if($branch){
            return $this->apiResponse(new BranchResource($branch),'ok',200);
        }
        return $this->apiResponse(null,'The branch Not Found',404);

    }

    
    public function store(Request $request){
       

        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'about' => 'nullable|string|min:3|max:2500',
            'image' => 'nullable|file|image|mimes:jpeg,jpg,png',
            'address' => 'nullable|string|min:3|max:2500',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(null,$validator->errors(),400);
        }

        $branch = Branch::create($request->all());
        if($request->hasFile('image')){
            $image = $request->file('image');
            $filename = $request->name.'.'.$image->getClientOriginalName();
            $request->image->move(public_path('/images/branch'),$filename);
            $branch->image = $filename;
        }

        if($branch){
            return $this->apiResponse(new BranchResource($branch),'The branch Save',201);
        }

        return $this->apiResponse(null,'The branch Not Save',400);
    }

    

    
    public function update(Request $request ,$id){

        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'about' => 'nullable|string|min:3|max:2500',
            'image' => 'nullable|file|image|mimes:jpeg,jpg,png',
            'address' => 'nullable|string|min:3|max:2500',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(null,$validator->errors(),400);
        }


        $branch=Branch::find($id);
        

        if(!$branch){
            return $this->apiResponse(null,'The branch Not Found',404);
        }

        $branch->update($request->all());
        if($request->hasFile('image')){
            $image = $request->file('image');
            $filename = $request->name.'.'.$image->getClientOriginalName();
            $request->image->move(public_path('/images/branch'),$filename);
            $branch->image = $filename;
        }

        if($branch){
            return $this->apiResponse(new BranchResource($branch),'The branch update',201);
        }

    }

    
    public function destroy($id){

        $branch=Branch::find($id);

        if(!$branch){
            return $this->apiResponse(null,'The branch Not Found',404);
        }

        $branch->delete($id);

        if($branch){
            return $this->apiResponse(null,'The branch deleted',200);
        }

    }
}
