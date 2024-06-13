<?php

namespace App\Http\Controllers;

use App\FlavourCategory;
use App\Http\Requests\StoreFlavourCategoryRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;

class AdminFlavourCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        if (Gate::allows('view-flavourCategory')) {
            $flavourCategories  = FlavourCategory::paginate(10);
            return view('admin.flavourCategories.index',compact('flavourCategories'));
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
    public function store(StoreFlavourCategoryRequest $request)
    {
        //
        if (Gate::allows('create-flavourCategory')) {

         $flavourCategory=   FlavourCategory::FirstOrCreate(['name'=>$request->input('name')],$request->all());
         if($flavourCategory->wasRecentlyCreated)
         {
             Session::flash('created_fcategory', 'Flavour Category Added');
             return redirect()->back();
         }
         else
         {
             Session::flash('created_fcategory', 'Flavour Category already exists');
             return redirect()->back();
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
        if (Gate::allows('update-flavourCategory')) {
            try{
            $flavourCategory = FlavourCategory::findOrFail($id);
            return view('admin.flavourCategories.edit',compact('flavourCategory'));
                }catch (ModelNotFoundException $exception)
            {
                return back()->withError("Flavour Category Not Found by ID".$id);
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
    public function update(StoreFlavourCategoryRequest $request, $id)
    {
        //
        if (Gate::allows('update-flavourCategory')) {
            $input = $request->all();
            try {
                FlavourCategory::findOrFail($id)->update($input);
                Session::flash('updated_fcategory', 'Product Category updated');
                return redirect('admin/flavourCategory');
            }catch (\Exception $exception)
            {
                return back()->withError("Failed to update Flavour Category ID".$id);
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
        if (Gate::allows('delete-flavourCategory')) {

            try {
                $flavourCategory = FlavourCategory::findOrFail($id);
                $flavourCategory->delete();
                Session::flash('deleted_fcategory', 'Product Category Deleted');
                return redirect('admin/flavourCategory');
            }catch (\Exception $exception)
            {
                return back()->withError("Failed To Delete Flavour Category ID".$id);
            }

        } else {
            return redirect('admin');
        }
    }
    public function search(Request $request)
    {

        $searchTerm = Input::post('search_term');
        $productStatus = Input::post('is_active');




        if ((empty($searchTerm) || is_null($searchTerm)) && $productStatus != -1) {
            $flavourCategories = FlavourCategory::where('is_active', '=', $productStatus)
                ->paginate(10);

            return view('admin.flavourCategories.index',compact('flavourCategories'));

        }
        else  if (!empty($searchTerm)&&$productStatus == -1)
        {
             $flavourCategories = FlavourCategory::  where('id','=',$searchTerm)
                ->orWhere('name','LIKE','%'.$searchTerm.'%')
                ->paginate(10);

            return view('admin.flavourCategories.index',compact('flavourCategories'));

        }
        else {

            $flavourCategories = FlavourCategory::where('is_active', '=', $productStatus)
                -> where(function ($query) use ($searchTerm){
                    $query->where('id','=',$searchTerm)
                        ->orWhere('name','LIKE','%'.$searchTerm.'%');
                })->paginate(10);

//
            return view('admin.flavourCategories.index',compact('flavourCategories'));

        }
    }
}
