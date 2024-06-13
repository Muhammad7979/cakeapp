<?php

namespace App\Http\Controllers;

use App\Http\Requests\MaterialStoreRequest;
use App\Material;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;

class AdminMaterialsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        if (Gate::allows('view-material')) {
        $materials  = Material::paginate(10);
        return view('admin.materials.index',compact('materials'));
        } else {
            return redirect('admin');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(MaterialStoreRequest $request)
    {
        //
        if (Gate::allows('create-material')) {


            try {
                $material = Material::firstOrCreate(['sku' => $request->input('sku')], $request->all());
                if ($material->wasRecentlyCreated) {
                    Session::flash('created_material', 'Material Added');
                    return redirect()->back();
                } else {
                    Session::flash('created_material', 'Material already exists');
                    return redirect()->back();

                }

                }catch (\Exception $exception)
            {
                Log::error("Fail to Create Material".$exception->getMessage());
                return back()->withError("Fail To Create Material");
            }



        } else {
            return redirect('admin');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        if (Gate::allows('update-material')) {
            try {
                $material = Material::findOrFail($id);
                return view('admin.materials.edit', compact('material'));
                }
                catch (ModelNotFoundException $exception)
                {
                    Log::error("Fail to Found Material".$exception->getMessage());
                    return back()->withError("Fail To Found Material ID".$id );
                }
        } else {
            return redirect('admin');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(MaterialStoreRequest $request, $id)
    {
        //
        if (Gate::allows('update-material')) {


            try {
                $input = $request->all();
                Material::findOrFail($id)->update($input);
                Session::flash('updated_material', 'Material Updated');
                return redirect('admin/materials');
                }
                catch (\Exception $exception)
                {
                    Log::error("Fail to Update Material".$exception->getMessage());
                    return back()->withError("Fail To Update Material ID".$id );
                }



        } else {
            return redirect('admin');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        if (Gate::allows('delete-material')) {



            try {
                $material = Material::findOrFail($id);
                $material->delete();
                Session::flash('deleted_material', 'Material Deleted');
                return redirect('admin/materials');
                }
                catch (\Exception $exception)
                {
                    Log::error("Fail to Delete Material".$exception->getMessage());
                    return back()->withError("Fail To Delete Material ID".$id );
                }


        } else {
            return redirect('admin');
        }
    }
    public function search(Request $request)
    {

//        $searchTerm = $request->input('search_term');
//        $productStatus = $request->input('is_active');
        $search_term =Input::get('search_term');
        $is_active = Input::get('is_active');



        if ((empty($search_term) || is_null($search_term)) && $is_active != -1) {
            $materials = Material::where('is_active', '=', $is_active)->paginate(10)->setPath ( '' );
            $materials = $materials->appends ( array (
                'search_term' => Input::get('search_term'),
                'is_active' => Input::get('is_active'),
            ) );

            return view('admin.materials.index',compact('materials'));

        }
        else  if (!empty($search_term)&&$is_active == -1)
        {
        $materials = Material::  where('sku','LIKE','%'.$search_term.'%')
                                    ->orWhere('name','LIKE','%'.$search_term.'%')
                                   ->paginate(10)->setPath ( '' );
            $materials = $materials->appends ( array (
                'search_term' => Input::get('search_term'),
                'is_active' => Input::get('is_active'),
            ) );

            return view('admin.materials.index',compact('materials'));

        }
        else {

            $materials = Material::where('is_active', '=', $is_active)
                -> where(function ($query) use ($search_term){
                    $query->where('sku','=',$search_term)
                        ->orWhere('name','LIKE','%'.$search_term.'%');
                })->paginate(10)->setPath ( '' );
            $materials = $materials->appends ( array (
                'search_term' => Input::get('search_term'),
                'is_active' => Input::get('is_active'),
            ) );

            return view('admin.materials.index',compact('materials'));

//

        }
    }
}
