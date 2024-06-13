<?php

namespace App\Http\Controllers;

use App\Category;
use App\Http\Requests\StoreCategoryRequest;
use App\Photo;
use DemeterChain\C;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class AdminCategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        if(Gate::allows('view-category')) {
            $categories = Category::paginate(10);
            return view('admin.categories.index', compact('categories'));
        }else{
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
        if (Gate::allows('create-category')) {
            try {
                $parentCategories = Category::where('parent_id', 0)->pluck('name', 'id')->all();
                $childCategories = Category::where('parent_id', '!=', 0)->pluck('name', 'id')->all();
                return view('admin.categories.create', compact('parentCategories', 'childCategories'));
            }catch (\Exception $exception)
            {
                Log::error('Error Getting Product Categories'.$exception->getMessage());

                return back()->withError("Error Getting Product Categories");
            }

        } else {
            return redirect('admin');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCategoryRequest $request)
    {
        //
        if(Gate::allows('create-category')) {

            $input = $request->all();

            if (isset($input)) {
                if ($file = $request->file('photo_id')) {

                    $name = $request->input('name') . '.' . $file->getClientOriginalExtension();

                    $file->move('images/Product_Categories', $name);

                    $photo = Photo::create(['path' => $name]);

                    $input['photo_id'] = $photo->id;
                    $input['photo_path'] = $name;
                }

//
                $category = Category::firstOrCreate(['name' => $request->input('name')], $input);

                if ($category->wasRecentlyCreated) {
                    Session::flash('created_category', 'The Product Category has been created');
                    return redirect('admin/categories');
                } else {
                    Session::flash('created_category', 'Product Category Already Exists');
                    return redirect('admin/categories');

                }
            }else
            {
                return back()->withError("Cannot Create Product Category");
            }
        }

        else
        {
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
        if (Gate::allows('update-category')) {
            try {
                $category = Category::findOrFail($id);
                $parentCategories = Category::where('parent_id', 0)->pluck('name', 'id')->all();
                $childCategories = Category::where('parent_id', '!=', 0)->pluck('name', 'id')->all();
                return view('admin.categories.edit', compact('category', 'parentCategories', 'childCategories'));
            }catch (ModelNotFoundException $exception)
            {
                return back()->withError("Category Not Found by ID".$id);
            }
        }
        else
        {
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
    public function update(StoreCategoryRequest $request, $id)
    {
        //
        if (Gate::allows('update-user')) {
            try {
            $category = Category::findOrFail($id);
            }catch (ModelNotFoundException $exception)
            {
                return back()->withError("Category Not Found by ID".$id);
            }
            // use the below code if you have allowed the user to skip the password field for updation  by creating a new edit request
            //this will assign all the fields except password if it is not set in the edit page
            // REMOVE THE bcrypt password statment from end ...if using this method
//        if(trim($request->password) =='')
//        {
//            $input=$request->except('password');
//        }else
//        {
//            $input = $request->all();
            //  $input['password']=bcrypt($request->password);
//        }


            $input = $request->all();
            if(isset($input))
            {
            if ($file = $request->file('photo_id')) {


//
                $name = $request->input('name').time().'.'.$file->getClientOriginalExtension();
                $file->move('images/Product_Categories/', $name);

                if ($category->photo_id == "" || $category->photo_id == null) {
                    $photo = Photo::create(['path' => $name]);

                    $input['photo_id'] = $photo->id;

                } else {

                    $photo = Photo::find($category->photo_id);
                    $photo->path = $name;
                    $photo->save();

                    $input['photo_id'] = $photo->id;
                    $input['photo_path'] = $name;

                }

            }


            $category->update($input);


       //     Session::flash('updated_user', 'The user has been updated');
            Session::flash('updated_category', 'Product Category Updated');
            return redirect('admin/categories');
                }
                else
                {
                    return back()->withError("Error Updating Category ID".$id);
                }
        }
        else
        {

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


        if (Gate::allows('delete-category')) {
            try {
                $category = Category::findOrFail($id);
                unlink(public_path() . "/images/Product_Categories/" . $category->photo_path);

                $category->delete();
                Session::flash('created_category', 'Product Category Deleted');


                return redirect('admin/categories');
            }catch (\Exception $exception)
            {
                Log::error("Error Deleting Product Category".$exception->getMessage());
                return back()->withError("Error Deleting Category ID".$id);
            }
            }
        else
        {

            return redirect('admin');
        }
    }
    public function search(Request $request)
    {

        $searchTerm = $request->input('search_term');
        $productStatus = $request->input('is_active');



        //    dd(array($convertedToDate));
        if ((empty($searchTerm) || is_null($searchTerm)) && $productStatus != -1) {
             $categories = Category::where('is_active', '=', $productStatus)
                ->with('parent')
                ->with('photo')
                ->paginate(10);

            return view('admin.categories.index',compact('categories'));

        }
        else  if (!empty($searchTerm)&&$productStatus == -1)
        {
            $categories = Category::
            with('parent')
                ->with('photo')
                ->where('id','=',$searchTerm)
                ->orWhere('name','LIKE','%'.$searchTerm.'%')
                ->orWhereHas('parent',function ($q) use($searchTerm){
                    $q->where('name','LIKE','%'.$searchTerm.'%');
                })
                ->paginate(10);;

            return view('admin.categories.index',compact('categories'));


        }
        else {

            $categories = Category::
                with('parent')
                ->with('photo')
                ->where('is_active', '=', $productStatus)
                -> where(function ($query) use ($searchTerm){
                    $query->where('id','=',$searchTerm)
                        ->orWhere('name','LIKE','%'.$searchTerm.'%')
                        ->orWhereHas('parent',function ($q) use($searchTerm){
                            $q->where('name','LIKE','%'.$searchTerm.'%');
                        });
                })->paginate(10);
            return view('admin.categories.index',compact('categories'));




        }
    }
}
