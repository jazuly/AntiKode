<?php

namespace App\Http\Controllers;

use App\Models\Outlet;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\OutletRequest;

class OutletController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $outlet = Outlet::selectRaw("*, trunc((point(-6.175136383245059, 106.82709913722499) <@> (point(longitude, latitude)::point))::NUMERIC * 1.609344, 2) || ' KM' as distance")
                ->orderBy("distance")
                ->get()
                ->load("brand:id,name");

            return response()->success($outlet, 200);
        } catch (\Throwable $th) {
            return response()->error($th->getCode(), $th->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\OutletRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(OutletRequest $request)
    {
        try {
            $outlet = new Outlet();
            $outlet->name = $request->input("name");
            $outlet->address = $request->input("address");
            $outlet->longitude = $request->input("longitude");
            $outlet->latitude = $request->input("latitude");
            $outlet->brand_id = $request->input("brand_id");

            if ($request->file("picture")) {
                $getNameOnly = collect(explode(".", $request->file("picture")->getClientOriginalName()))
                    ->filter(fn($data, $idx) => $idx + 1 != count(explode(".", $request->file("picture")->getClientOriginalName())))
                    ->implode(".");
        
                $fName = Str::random(5)."-".Str::slug($getNameOnly).".".$request->file("picture")->getClientOriginalExtension();
                Storage::disk('public')->put($fName, fopen($request->file("picture"), 'r+'));

                $outlet->picture = $fName;
            }

            $outlet->save();

            return response()->success($this->showSimple($outlet->id), 200);
        } catch (\Throwable $th) {
            return response()->error($th->getCode(), $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Outlet  $outlet
     * @return \Illuminate\Http\Response
     */
    public function show(Outlet $outlet)
    {
        try {
            return response()->success($outlet->load("brand:id,name"), 200);
        } catch (\Throwable $th) {
            return response()->error($th->getCode(), $th->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\OutletRequest  $request
     * @param  \App\Models\Outlet  $outlet
     * @return \Illuminate\Http\Response
     */
    public function update(OutletRequest $request, Outlet $outlet)
    {
        try {
            $input = $request->only("name", "picture", "address", "longitude", "latitude", "brand_id");

            if ($request->file("picture")) {
                $getNameOnly = collect(explode(".", $request->file("picture")->getClientOriginalName()))
                    ->filter(fn($data, $idx) => $idx + 1 != count(explode(".", $request->file("picture")->getClientOriginalName())))
                    ->implode(".");
        
                $fName = Str::random(5)."-".Str::slug($getNameOnly).".".$request->file("picture")->getClientOriginalExtension();
                Storage::disk('public')->put($fName, fopen($request->file("picture"), 'r+'));

                $input["picture"] = $fName;
            }

            $outlet->update($input);

            return response()->success($this->showSimple($outlet->id), 200);
        } catch (\Throwable $th) {
            return response()->error($th->getCode(), $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Outlet  $outlet
     * @return \Illuminate\Http\Response
     */
    public function destroy(Outlet $outlet)
    {
        try {
            $outlet->delete();

            return response()->success(null, 204);
        } catch (\Throwable $th) {
            return response()->error($th->getCode(), $th->getMessage());
        }
    }

    private function showSimple($id)
    {
        return Outlet::where("id", $id)
            ->selectRaw("*, trunc((point(-6.175136383245059, 106.82709913722499) <@> (point(longitude, latitude)::point))::NUMERIC * 1.609344, 2) || ' KM' as distance")
            ->first()
            ->load("brand:id,name");
    }
}
