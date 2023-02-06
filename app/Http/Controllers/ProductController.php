<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\ProductRequest;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            return response()->success(Product::get()->load("brand:id,name"), 200);
        } catch (\Throwable $th) {
            return response()->error($th->getCode(), $th->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\ProductRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductRequest $request)
    {
        try {
            $product = new Product();
            $product->name = $request->input("name");
            $product->price = $request->input("price");
            $product->brand_id = $request->input("brand_id");

            if ($request->file("picture")) {
                $getNameOnly = collect(explode(".", $request->file("picture")->getClientOriginalName()))
                    ->filter(fn($data, $idx) => $idx + 1 != count(explode(".", $request->file("picture")->getClientOriginalName())))
                    ->implode(".");
        
                $fName = Str::random(5)."-".Str::slug($getNameOnly).".".$request->file("picture")->getClientOriginalExtension();
                Storage::disk('public')->put($fName, fopen($request->file("picture"), 'r+'));

                $product->picture = $fName;
            }

            $product->save();

            return response()->success($product, 200);
        } catch (\Throwable $th) {
            return response()->error($th->getCode(), $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        try {
            return response()->success($product->load("brand:id,name"), 200);
        } catch (\Throwable $th) {
            return response()->error($th->getCode(), $th->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\ProductRequest  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(ProductRequest $request, Product $product)
    {
        try {
            $input = $request->only("name", "price", "picture", "brand_id");

            if ($request->file("picture")) {
                $getNameOnly = collect(explode(".", $request->file("picture")->getClientOriginalName()))
                    ->filter(fn($data, $idx) => $idx + 1 != count(explode(".", $request->file("picture")->getClientOriginalName())))
                    ->implode(".");
        
                $fName = Str::random(5)."-".Str::slug($getNameOnly).".".$request->file("picture")->getClientOriginalExtension();
                Storage::disk('public')->put($fName, fopen($request->file("picture"), 'r+'));

                $input["picture"] = $fName;
            }

            $product->update($input);

            return response()->success($product->fresh(), 200);
        } catch (\Throwable $th) {
            return response()->error($th->getCode(), $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        try {
            $product->delete();

            return response()->success(null, 204);
        } catch (\Throwable $th) {
            return response()->error($th->getCode(), $th->getMessage());
        }
    }
}
