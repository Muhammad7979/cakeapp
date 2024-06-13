<?php

namespace App\Http\Controllers;

use App\Category;
use App\Configuration;
use App\Flavour;
use App\FlavourCategory;
use App\Http\Requests\StoreProductRequest;
use App\Material;
use App\Photo;
use App\Product;
use Carbon\Carbon;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;

class AdminProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        if (Gate::allows('view-product')) {


            $products = Product::paginate(10);
            return view('admin.products.index', compact('products'));
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
        if (Gate::allows('create-user')) {
            $flavours = Flavour::pluck('name', 'id')->all();
            $categories = Category::where('parent_id', '!=', 0)->pluck('name', 'id')->all();
            $materials = Material::pluck('name', 'id')->all();
            return view('admin.products.create', compact('categories', 'materials', 'flavours'));
        } else {
            return redirect('admin');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreProductRequest $request)
    {
        //
        if (Gate::allows('create-product')) {
            $input = $request->except(['flavour_id', 'material_id']);
            if ($file = $request->file('photo_id')) {

//                $name = time() . $file->getClientOriginalName();
                $name = $request->input('sku') . '.' . $file->getClientOriginalExtension();

                $file->move('images/Product_Images/', $name);

                $photo = Photo::create(['path' => $name]);

                $input['photo_id'] = $photo->id;
                $input['photo_path'] = $name;
            }


            $product = Product::firstOrCreate(['sku' => $input['sku']], $input);

            if ($product->wasRecentlyCreated) {
                $product->flavours()->sync($request->input('flavour_id'));
                $product->materials()->sync($request->input('material_id'));

                Session::flash('created_product', 'Product Added');
                return redirect('admin/products');
            } else {
                Session::flash('created_product', 'Product Already Exits');
                return redirect('admin/products');

            }


//
        } else {
            return redirect('admin');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        if (Gate::allows('update-product')) {

            try {
                $product = Product::findOrFail($id);
                }catch (ModelNotFoundException $exception)
                    {
                        return back()->withError("Product Not Found by ID".$id);
                    }

            $flavours = Flavour::pluck('name', 'id')->all();
            $categories = Category::where('parent_id', '!=', 0)->pluck('name', 'id')->all();
            $materials = Material::pluck('name', 'id')->all();
            return view('admin.products.edit', compact('product', 'flavours', 'categories', 'materials'));
        } else {
            return redirect('admin');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreProductRequest $request, $id)
    {
        //
        if (Gate::allows('update-product')) {

            try {
            $product = Product::findOrFail($id);
            }catch (ModelNotFoundException $exception)
            {
                return back()->withError("Product Not Found by ID".$id);
            }


                $unlink = Configuration::where('key', '=', 'Unlink_product_image')->get();

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

            $input = $request->except(['flavour_id', 'material_id']);

            if ($file = $request->file('photo_id')) {


//                $name = time() .  str_replace(' ', '',$file->getClientOriginalName());
                $name = $request->input('sku') .time(). '.' . $file->getClientOriginalExtension();

                $file->move('images/Product_Images/', $name);

                if ($product->photo_id == "" || $product->photo_id == null) {
                    $photo = Photo::create(['path' => $name]);
                    $input['photo_id'] = $photo->id;
                    $input['photo_path'] = $name;

                    if($unlink->count()>0) {

                        if ($unlink->value == 1) {
                            unlink(public_path() . 'images/Product_Images/' . $product->photo->path);
                                               }
                    }

                } else {


                    $photo = Photo::find($product->photo_id);
                    $photo->path = $name;
                    $photo->save();
//                $elseif = $user->photo_id;
                    $input['photo_id'] = $photo->id;
                    $input['photo_path'] = $name;

                }

            }


            $product->update($input);
            $product->flavours()->sync($request->input('flavour_id'));
            $product->materials()->sync($request->input('material_id'));

            Session::flash('updated_product', 'Product Updated');
            return redirect('admin/products');
        } else {

            return redirect('admin');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //

        if (Gate::allows('delete-user')) {

            try{
            $product = Product::findOrFail($id);
            }catch (ModelNotFoundException $exception)
            {
                return back()->withError("Product Not Found by ID".$id);
            }
//            unlink(public_path() . $product->photo->path);

            $product->delete();

            Session::flash('deleted_product', 'Product Deleted');
            return redirect('admin/products');
        } else {

            return redirect('admin');
        }
    }

    public function search(Request $request)
    {
//
//        $searchTerm = $request->input('search_term');
//        $productStatus = $request->input('is_active');

        $search_term =Input::get('search_term');
        $is_active = Input::get('is_active');


        if ((empty($search_term) || is_null($search_term)) && $is_active != -1) {
            $products = Product::where('is_active', '=', $is_active)
                ->with('photo')
                ->with('category')
                ->paginate(10)->setPath ( '' );
            $products = $products->appends ( array (
                'search_term' => Input::get('search_term'),
                'is_active' => Input::get('is_active'),
            ) );
            return view('admin.products.index',compact('products'));

        } else if (!empty($search_term) && $is_active == -1) {
            $products = Product::
                  with('photo')
                ->with('category')
                -> where('sku','LIKE','%'.$search_term.'%')
                ->orWhere('name','LIKE','%'.$search_term.'%')
                ->orWhereHas('category',function ($q) use($search_term){
                    $q->where('name','LIKE','%'.$search_term.'%');
                })
                ->paginate(10)->setPath ( '' );
//                ->get();  // for testing purposes
            $products = $products->appends ( array (
                'search_term' => Input::get('search_term'),
                'is_active' => Input::get('is_active'),
            ) );



            return view('admin.products.index',compact('products'));
//
        } else {

            $products = Product::with('photo')
                ->with('category')
                ->  where('is_active', '=', $is_active)
                -> where(function ($query) use ($search_term){
                    $query->where('sku','LIKE','%'.$search_term.'%')
                            ->orWhere('name','LIKE','%'.$search_term.'%')
                             ->orWhereHas('category',function ($q) use($search_term){
                            $q->where('name','LIKE','%'.$search_term.'%');
                        });
                    })->paginate(10)->setPath ( '' );
//                ->get();
            $products = $products->appends ( array (
                'search_term' => Input::get('search_term'),
                'is_active' => Input::get('is_active'),
            ) );
//
            return view('admin.products.index',compact('products'));




//            return view('admin.products.index',compact('products'));
//


        }
    }

    public function getProductsLive()
    {

        $products = Product::all();

        if($products->count()<=0)
        {
            $syncAll =1;
        }else
        {
            $syncAll=0;
        }

        try
        {
            $updateRange = Configuration::where('key','=','Product_update_range')->first()->value;

        }catch (\Exception $exception)
        {
            $updateRange=3;
            Log::error("Update Product Range not given");
        }


        $client = new \GuzzleHttp\Client(['base_uri' => env('Live_LINK'), 'headers' => ['Accept' => 'application/json']]);

        try {
            $response = $client->request('POST', 'api/products/live',
                [
                    'form_params' => [
                        'sync_all' => $syncAll,
                        'update_range' => $updateRange,

                    ]
                ]);
        } catch (GuzzleException $e) {

            Log::error("Error Syncing Products From Live".$e->getMessage());
            return back()->withError("Error Syncing Products From Live");
        }


        $data = $response->getBody()->getContents();

        $decodedData = json_decode($data);

//

        if(!empty($decodedData))
        {
            $result= ["status"=>"Products Updated"];
        }else
        {
            $result= "Nothing To Update, Products are in Sync with Live";
            return $result;
        }

        $this->syncFlavourCategories($decodedData[0]);
        $this->syncFlavours($decodedData[1]);
        $this->syncMaterials($decodedData[2]);
        $this->syncProductCategories($decodedData[3]);
        $this->syncProducts($decodedData[4]);

        return $result;

    }

    public function getProductsLiveSync(Request $request)
    {
        $syncAll = $request->input('sync_all');
        $updateRange=     $request->input('update_range');

        $fromDate=    Carbon::now()->subDays($updateRange);
        $toDate=     Carbon::now();


        if($syncAll) {
            $flavourCategory = FlavourCategory::all();
            $flavours = Flavour::all();
            $materials = Material::all();
            $productCategory = Category::all();

            $products = Product::
            with('photo')->get();

            foreach ($products as $product) {

                $product_flavours = DB::select('select flavour_id from flavour_product where product_id = :id', ['id' => $product->id]);
                $product_materials = DB::select('select material_id from material_product where product_id = :id', ['id' => $product->id]);

                $product['flavour_p'] = $product_flavours;
                $product['material_p'] = $product_materials;

            }


            $query = array($flavourCategory, $flavours, $materials, $productCategory, $products);

            return response()->json($query);
            }
            else
            {
                $flavourCategory = FlavourCategory::whereBetween('updated_at', [$fromDate, $toDate])->get();
                $flavours = Flavour::whereBetween('updated_at', [$fromDate, $toDate])->get();
                $materials = Material::whereBetween('updated_at', [$fromDate, $toDate])->get();
                $productCategory = Category::whereBetween('updated_at', [$fromDate, $toDate])->get();

                $products = Product::whereBetween('updated_at', [$fromDate, $toDate])
                            ->with('photo')->get();

                foreach ($products as $product) {

                    $product_flavours = DB::select('select flavour_id from flavour_product where product_id = :id', ['id' => $product->id]);
                    $product_materials = DB::select('select material_id from material_product where product_id = :id', ['id' => $product->id]);

                    $product['flavour_p'] = $product_flavours;
                    $product['material_p'] = $product_materials;

                }


                $query = array($flavourCategory, $flavours, $materials, $productCategory, $products);

                return response()->json($query);


            }
    }

    private function syncFlavourCategories($data)
    {


        if(!empty($data)) {
            try {
                foreach ($data as $flavourCategory) {

                    $record = (array)$flavourCategory;
                    FlavourCategory::create($record);
                }
            } catch (\Exception $exception) {
                Log::error("Error Syncing Flavour Categories From Live" . $exception->getMessage());
            }


        }

    }

    private function syncFlavours($data)
    {
//        Flavour::truncate();
        if (!empty($data)) {

            try {
            foreach ($data as $flavour) {
                $flavourSku[] = $flavour->sku;



                $flavour = Flavour::updateOrCreate(['sku' => $flavour->sku],
                    ['name' => $flavour->name,
                        'id' => $flavour->id,
                        'price' => $flavour->price,
                        'flavourCategory_id' => $flavour->flavourCategory_id,
                        'is_active' => $flavour->is_active,
                        'created_at' => $flavour->created_at,
                        'updated_at' => $flavour->updated_at]);


            }
              }
                 catch (\Exception $exception)
                 {
                 Log::error("Error Syncing Flavours From Live" . $exception->getMessage());
                 }


//            Flavour::whereNotIn('sku', $flavourSku)->delete();
        }




    }

    private function syncMaterials($data)
    {

        if (!empty($data)) {
            try {

            foreach ($data as $material) {
                $materialSku[] = $material->sku;



                $material = Material::updateOrCreate(['sku' => $material->sku],
                    ['name' => $material->name,
                        'id' => $material->id,
                        'price' => $material->price,
                        'is_active' => $material->is_active,
                        'created_at' => $material->created_at,
                        'updated_at' => $material->updated_at]);


            }
            }
            catch (\Exception $exception)
            {
                Log::error("Error Syncing Materials From Live" . $exception->getMessage());
            }
//            Material::whereNotIn('sku', $materialSku)->delete();



        }

    }

    private function syncProductCategories($data)
    {
        if (!empty($data)) {


            try {

                foreach ($data as $productCategory) {


                    $product_category = Category::updateOrCreate(['name' => $productCategory->name],
                        ['name' => $productCategory->name,
                            'id' => $productCategory->id,
                            'parent_id' => $productCategory->parent_id,
                            'description' => $productCategory->description,
                            'photo_id' => $productCategory->photo_id,
                            'photo_path' => $productCategory->photo_path,
                            'is_active' => $productCategory->is_active,
                            'created_at' => $productCategory->created_at,
                            'updated_at' => $productCategory->updated_at]);
                    
                    
                    $id = $product_category->id;
                    $updatedProductCategory = Category::findOrFail($id);
//
//
//
//
//
                    if ((!(Photo::where('path', '=', $updatedProductCategory->photo_path)->count() > 0))) {
                        // id doesnt exists
                        $temp_path = public_path() . '/images/Sync_Product_Categories/' . $updatedProductCategory->photo_path;
                        $fixed_path = public_path() . '/images/Product_Categories/' . $updatedProductCategory->photo_path;
                        $file_path_handle = fopen($temp_path, 'w');
//                $pictureArray[]=$updatedProduct->sku;
                        $client = new \GuzzleHttp\Client(['base_uri' => env('Live_LINK')]);
                        try {
                            $client->request('get', 'images/Product_Categories/' . $updatedProductCategory->photo_path, ['sink' => $file_path_handle]);

                            $photo = Photo::create(['path' => $updatedProductCategory->photo_path]);


                            if (is_resource($file_path_handle)) {
                                fclose($file_path_handle);
                            }
                        } catch (GuzzleException $e) {
                            Log::error("Error Syncing Product Categories From Live" . $e->getMessage());
                        }
                        File::move($temp_path, $fixed_path);
                        $updatedProductCategory->photo_id = $photo->id;
                        $updatedProductCategory->save();


                    } else if ((Photo::where('path', '=', $updatedProductCategory->photo_path)->count() > 0)) {
                        $existing_photo = Photo::where('path', '=', $updatedProductCategory->photo_path)->first();
                        $updatedProductCategory->photo_id = $existing_photo->id;
                        $updatedProductCategory->save();
                    }


                }
            }  catch (\Exception $exception)
            {
                Log::error("Error Syncing Product Categories From Live" . $exception->getMessage());
            }

        }
    }

    private function syncProducts($data)
    {

        if (!(empty($data))) {

            try {

            foreach ($data as $product) {

                $productSku[] = $product->sku;

//            $record= (array)$flavour;
//            Flavour::create($record);

                $product_created = Product::updateOrCreate(['sku' => $product->sku],
                    ['name' => $product->name,
                        'live_synced' => $product->live_synced,
                        'category_id' => $product->category_id,
                        'weight' => $product->weight,
                        'price' => $product->price,
                        'photo_id' => $product->photo_id,
                        'photo_path' => $product->photo_path,
                        'is_active' => $product->is_active,
                        'created_at' => $product->created_at,
                        'updated_at' => $product->updated_at]);
                $id = $product_created->id;
                $updatedProduct = Product::findOrFail($id);
//

//
                if (count($product->flavour_p) > 0){
                    foreach ($product->flavour_p as $flavour) {
//
                        $flavours[] = $flavour->flavour_id;



                    }
                $updatedProduct->flavours()->sync($flavours);
                unset($flavours);
            }

            if(count($product->material_p)) {
                foreach ($product->material_p as $material) {
                    $materials[] = $material->material_id;


                }
                $updatedProduct->materials()->sync($materials);
                unset($materials);
            }

                // id doesnt exists
                if (!(Photo::where('path', '=', $updatedProduct->photo_path)->count() > 0)) {
//                $pictureArray[]=$updatedProduct->sku;
                    $temp_path = public_path() . '/images/Sync_Product_Images/' . $updatedProduct->photo_path;
                    $fixed_path = public_path() . '/images/Product_Images/' . $updatedProduct->photo_path;
                    $file_path_handle = fopen($temp_path, 'w');


                    $client = new \GuzzleHttp\Client(['base_uri' => env('Live_LINK')]);
                    try {
                        $client->request('get', 'images/Product_Images/' . $updatedProduct->photo_path, ['sink' => $temp_path]);

                        $photo = Photo::create(['path' => $updatedProduct->photo_path]);

                        if (is_resource($file_path_handle)) {
                            fclose($file_path_handle);
                        }

                    } catch (GuzzleException $e) {
                        Log::error("Error Syncing Product Images From Live".$e->getMessage());
                    }

                    File::move($temp_path, $fixed_path);

                    $updatedProduct->photo_id = $photo->id;
                    $updatedProduct->save();

                } else if (($existing_photo = Photo::where('path', '=', $updatedProduct->photo_path)->count() > 0)) {
                    $existing_photo = Photo::where('path', '=', $updatedProduct->photo_path)->first();
                    $updatedProduct->photo_id = $existing_photo->id;
                    $updatedProduct->save();
                }


            }
            }
            catch (\Exception $exception)
            {
                Log::error("Error Syncing Products From Live" . $exception->getMessage());
            }



//            Product::whereNotIn('sku', $productSku)->delete();

        }
    }

            }


