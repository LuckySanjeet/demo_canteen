<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    use HttpResponses;
    public function store(Request $request)
    {
       try{
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'category' => 'required|in:snacks,meals,beverages'
        ]);

        $product = Product::create($validated);

        return $this->success('', 'Product Created Successfully', 200);
       }catch(ValidationException $e){
            return $this->error('', $e->errors()->all());
       }catch (\Exception $e) {
        return $this->error([], 'An error occurred');
        }
    }

    public function delete($id){
        try{
            Product::find($id)->update(['status'=>0]);
            return $this->success('','Product Deleted Successfully',200);
        }catch (\Exception $e) {
            return $this->error([], 'An error occurred');
        }
    }
// product list for admin, staff, students
    public function getProductList(){
        $products = Product::where('status', 1)->get()->toArray();
        return $this->success($products,'');
    }

    public function edit(Request $request){
        try{
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'price' => 'required|numeric',
                'category' => 'required|in:snacks,meals,beverages'
            ]);
            $product = Product::find($request->id)->update([
                'name'=>trim($request->name),
                'price'=> trim($request->price),
                'category' =>$request->category]);

             return $this->success('', 'Product updated successfully');
        }catch(ValidationException $e){
            return $this->error('', $e->errors()->all());
        }catch (\Exception $e) {
            return $this->error([], 'An error occurred');
        }

    }

    //product list for only admin
    public function getProducts(){
        $products= Product::all();
        return $this->success($products);
    }
}
