<?php

namespace App\Http\Controllers;

use App\Flavour;
use App\FlavourCategory;
use App\Http\Requests\StoreFlavourRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class AdminFlavoursController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        if (Gate::allows('view-flavour')) {
            $flavours = Flavour::paginate(10);

            return view('admin.flavours.index',compact('flavours'));
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

        if(Gate::allows('create-flavour')) {


            $flavourCategories= FlavourCategory::pluck('name', 'id')->all();

            return view('admin.flavours.create', compact('flavourCategories' ));
        }
        else
        {
            return redirect('admin');
        }

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreFlavourRequest $request)
    {
        //

        if (Gate::allows('create-flavour')) {

            try {
                $flavour = Flavour::firstOrCreate(['sku' => $request->input('sku')], $request->all());
                if ($flavour->wasRecentlyCreated) {
                    Session::flash('created_flavour', 'Flavour Added');
                    return redirect('admin/flavours');
                } else {
                    Session::flash('created_flavour', 'Flavour Already Exists');
                    return redirect('admin/flavours');

                }


            }catch (\Exception $exception)
            {
                Log::error("Error Creating Flavour".$exception->getMessage());
                return back()->withError("Failed To Create Flavour ");
            }
        }else {
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
        if (Gate::allows('update-flavour')) {

            try{
            $flavour = Flavour::findOrFail($id);
                }catch (ModelNotFoundException $exception)
            {
                return back()->withError("Flavour Not Found by ID".$id);
            }
            $flavourCategories = FlavourCategory::pluck('name', 'id')->all();
            return view('admin.flavours.edit',compact('flavour','flavourCategories'));
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
    public function update(StoreFlavourRequest $request, $id)
    {
        //
        if (Gate::allows('update-flavour')) {
            $input = $request->all();
            try {
                Flavour::findOrFail($id)->update($input);

                Session::flash('updated_flavour', 'Flavour Updated');
                return redirect('admin/flavours');
            }catch (\Exception $exception)
            {
                Log::error('Failed To Update Flavour'.$exception->getMessage());
                return back()->withError("Failed To Update Flavour ID".$id);
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

        if (Gate::allows('delete-flavour')) {
            try {

                $flavour = Flavour::findOrFail($id);
                $flavour->delete();
                Session::flash('deleted_flavour', 'Flavour Deleted');
                return redirect('admin/flavours');

               }
               catch (ModelNotFoundException $exception)
                  {
                Log::error('Failed To Update Flavour'.$exception->getMessage());
                return back()->withError("Failed To Delete Flavour ID".$id);
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
             $flavours = Flavour::where('is_active', '=', $is_active)
                ->with('flavourCategory')
                ->paginate(10)->setPath ( '' );
            $flavours = $flavours->appends ( array (
                'search_term' => Input::get('search_term'),
                'is_active' => Input::get('is_active'),
            ) );
            return view('admin.flavours.index',compact('flavours'));

        }
        else  if (!empty($search_term)&&$is_active == -1)
        {
            $flavours = Flavour::
            with('flavourCategory')
                -> where('sku','=',$search_term)
                ->orWhere('name','LIKE','%'.$search_term.'%')
                ->orWhereHas('flavourCategory',function ($q) use($search_term){
                    $q->where('name','LIKE','%'.$search_term.'%');
                })
                ->paginate(10)->setPath ( '' );
            $flavours = $flavours->appends ( array (
                'search_term' => Input::get('search_term'),
                'is_active' => Input::get('is_active'),
            ) );

//

            return view('admin.flavours.index',compact('flavours'));
        }
        else {

            $flavours = Flavour::where('is_active', '=', $is_active)
                ->with('flavourCategory')
                ->  where('is_active', '=', $is_active)
                       -> where(function ($query) use ($search_term){
                            $query->where('sku','=',$search_term)
                        ->orWhere('name','LIKE','%'.$search_term.'%')
                        ->orWhereHas('flavourCategory',function ($q) use($search_term){
                            $q->where('name','LIKE','%'.$search_term.'%');
                        });
                })->paginate(10)->setPath ( '' );
            $flavours = $flavours->appends ( array (
                'search_term' => Input::get('search_term'),
                'is_active' => Input::get('is_active'),
            ) );

            return view('admin.flavours.index',compact('flavours'));
//
//


        }
    }
}
