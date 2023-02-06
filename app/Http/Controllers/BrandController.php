<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\BrandRequest;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $data = Brand::withCount("product")
                ->get()
                ->load("outlet")
                ->map(function($d) {
                    $nData = $d;
                    $nData["closes_outlet"] = $d->outlet->count() > 0 ? $d->outlet[0]->distance : 'N/A';

                    return $nData;
                })
                ->sortBy("closes_outlet")
                ->values();

            return response()->success($data, 200);
        } catch (\Throwable $th) {
            return response()->error($th->getCode(), $th->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\BrandRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BrandRequest $request)
    {
        try {
            $brand = new Brand();
            $brand->name = $request->input("name");

            if ($request->file("logo")) {
                $getNameOnly = collect(explode(".", $request->file("logo")->getClientOriginalName()))
                    ->filter(fn($data, $idx) => $idx + 1 != count(explode(".", $request->file("logo")->getClientOriginalName())))
                    ->implode(".");
        
                $fName = Str::random(5)."-".Str::slug($getNameOnly).".".$request->file("logo")->getClientOriginalExtension();
                Storage::disk('public')->put($fName, fopen($request->file("logo"), 'r+'));

                $brand->logo = $fName;
            }

            if ($request->file("banner")) {
                $getNameOnly = collect(explode(".", $request->file("banner")->getClientOriginalName()))
                    ->filter(fn($data, $idx) => $idx + 1 != count(explode(".", $request->file("banner")->getClientOriginalName())))
                    ->implode(".");
        
                $fName = Str::random(5)."-".Str::slug($getNameOnly).".".$request->file("banner")->getClientOriginalExtension();
                Storage::disk('public')->put($fName, fopen($request->file("banner"), 'r+'));

                $brand->banner = $fName;
            }

            $brand->save();

            return response()->success($brand, 200);
        } catch (\Throwable $th) {
            return response()->error($th->getCode(), $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Brand  $brand
     * @return \Illuminate\Http\Response
     */
    public function show(Brand $brand)
    {
        try {
            return response()->success($brand->load(["outlet:id,name", "product:id,name"]), 200);
        } catch (\Throwable $th) {
            return response()->error($th->getCode(), $th->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\BrandRequest  $request
     * @param  \App\Models\Brand  $brand
     * @return \Illuminate\Http\Response
     */
    public function update(BrandRequest $request, Brand $brand)
    {
        try {
            $input = $request->only("name", "logo", "banner");

            if ($request->file("logo")) {
                $getNameOnly = collect(explode(".", $request->file("logo")->getClientOriginalName()))
                    ->filter(fn($data, $idx) => $idx + 1 != count(explode(".", $request->file("logo")->getClientOriginalName())))
                    ->implode(".");
        
                $fName = Str::random(5)."-".Str::slug($getNameOnly).".".$request->file("logo")->getClientOriginalExtension();
                Storage::disk('public')->put($fName, fopen($request->file("logo"), 'r+'));

                $input["logo"] = $fName;
            }

            if ($request->file("banner")) {
                $getNameOnly = collect(explode(".", $request->file("banner")->getClientOriginalName()))
                    ->filter(fn($data, $idx) => $idx + 1 != count(explode(".", $request->file("banner")->getClientOriginalName())))
                    ->implode(".");
        
                $fName = Str::random(5)."-".Str::slug($getNameOnly).".".$request->file("banner")->getClientOriginalExtension();
                Storage::disk('public')->put($fName, fopen($request->file("banner"), 'r+'));

                $input["banner"] = $fName;
            }

            $brand->update($input);

            return response()->success($brand->fresh(), 200);
        } catch (\Throwable $th) {
            return response()->error($th->getCode(), $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Brand  $brand
     * @return \Illuminate\Http\Response
     */
    public function destroy(Brand $brand)
    {
        try {
            $brand->delete();

            return response()->success(null, 204);
        } catch (\Throwable $th) {
            return response()->error($th->getCode(), $th->getMessage());
        }
    }
}
