<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\OfferResource;


class OfferController extends Controller
{
    use ApiResponseTrait;
    
    public function index(Request $request)
    {
        
        $offers = OfferResource::collection(Offer::get());
        return $this->apiResponse($offers,'success',200);
    }

    public function show($id){

        $offer = Offer::find($id);
        

        if($offer){
            return $this->apiResponse(new OfferResource($offer),'ok',200);
        }
        return $this->apiResponse(null,'The offer Not Found',404);

    }

    
    public function store(Request $request){

        $validator = Validator::make($request->all(), [
          
            'image' => 'nullable|file||image|mimes:jpeg,jpg,png',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(null,$validator->errors(),400);
        }

        $offer = Offer::create($request->all());
        if($request->hasFile('image')){
            foreach($request->file('image') as $image){
                $filename = $request->name.'.'.$image->getClientOriginalName();
                $request->image->move(public_path('/images/offer'),$filename);
                $offer->image = $filename;
            }
           
           
        }

        if($offer){
            return $this->apiResponse(new OfferResource($offer),'The offer Save',201);
        }

        return $this->apiResponse(null,'The offer Not Save',400);
    }

    

    
    public function update(Request $request ,$id){

        $validator = Validator::make($request->all(), [
            
            'image' => 'nullable|file||image|mimes:jpeg,jpg,png',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(null,$validator->errors(),400);
        }


        $offer =Offer::find($id);
        
        

        if(!$offer){
            return $this->apiResponse(null,'The offer Not Found',404);
        }

        $offer->update($request->all());
        if($request->hasFile('image')){
            foreach($request->file('image') as $image){
                $filename = $request->name.'.'.$image->getClientOriginalName();
                $request->image->move(public_path('/images/offer'),$filename);
                $offer->image = $filename;
            }
           
           
        }

        if($offer){
            return $this->apiResponse(new OfferResource($offer),'The offer update',201);
        }

    }

    
    public function destroy($id){

        $offer=Offer::find($id);

        if(!$offer){
            return $this->apiResponse(null,'The offer Not Found',404);
        }

        $offer->delete($id);

        if($offer){
            return $this->apiResponse(null,'The offer deleted',200);
        }

    }
}
