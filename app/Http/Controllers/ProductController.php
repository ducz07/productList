<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Product;
use Auth;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        if(request()->ajax()) {
            return datatables()->of(Product::select('*'))
            ->addColumn('action', 'action')
            ->addColumn('image', 'image')
            ->rawColumns(['action','image'])
            ->addIndexColumn()
            ->make(true);
        }
        return view('index');
    }

    public function store(Request $request)
    {  
        // dd($request->all());

        $productId = $request->id;
        if($productId){
             
            $product = Product::find($productId);
            if($request->hasFile('image')){
                $path = $request->file('image')->store('public/images');
                $product->image = $path;
            }   
         }else{
                $path = $request->file('image')->store('public/images');
               $product = new Product;
               $product->image = $path;
         }
         
        $product->product_name = $request->product_name;
        $product->description = $request->description;
        $product->qty = $request->qty;
        $product->price = $request->price;
        $product->uploaded_by = Auth::user()->name;
        $product->save();
     
        return Response()->json($product);
    }

    public function edit(Request $request)
    {   
        $where = array('id' => $request->id);
        $product  = Product::where($where)->first();
     
        return Response()->json($product);
    }

    public function destroy(Request $request)
    {
        $product = Product::where('id',$request->id);

        if($product->delete()){
            return response()->json([
                'msg' => 'The product was successfully deleted.',
                'status' => 200
            ]);
        }else{
            return response()->json([
                'msg' => 'A problem occured. Please try again!',
                'status' => 201
            ]);
        }
    }

}
