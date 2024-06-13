<?php

namespace App\Http\Controllers;

use App\Flavour;
use App\FlavourCategory;
use App\Photo;
use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ImportDataController extends Controller
{
    //use Excel;

    public function importExport()
    {
        return view('admin.import.importData');
    }

    public function importProductCategories(Request $request)
    {

    }
    public function importFlavourCategories(Request $request)
    {

    }
    public function importFlavour(Request $request)
    {

        $request->validate([
            'import_file' => 'required'
        ]);

        $path = $request->file('import_file')->getRealPath();
        $data = Excel::load($path)->get();

        if($data->count()){
            foreach ($data as $key => $value) {


                $flavourCategory = FlavourCategory::where('name','like','%'.$value->category.'%')->first();

                Log::info('Flavour Category'.$flavourCategory->id);
                $arr[] = ['name'=>$value->name, 'price'=>$value->price,'is_active'=>'1','sku'=>'FV-'.$value->sr,'flavourCategory_id'=>$flavourCategory->id];
//                $arr[] = ['title' => $value->title, 'description' => $value->description];


            }

            if(!empty($arr)){
                Flavour::insert($arr);
            }
        }

        return back()->with('success', 'Insert Record successfully.');


    }
    public function importProduct(Request $request)
    {

        $request->validate([
            'import_file' => 'required'
        ]);

        $path = $request->file('import_file')->getRealPath();
        $data = Excel::load($path)->get();

        if($data->count()){
            $counter=2;
            foreach ($data as $key => $value) {


                if(!empty($value->image))
                {
                    $photo = Photo::create(['path' => $value->image]);
//
////                Log::info('Product'.$value->image);
                $arr[] = ['category_id'=>$value->category_id, 'name'=>$value->name,'weight'=>$value->weight,'price'=>$value->price,'is_active'=>'1','sku'=>'CK-'.$counter,'photo_path'=>$value->image,'photo_id'=>$photo->id,'live_synced'=>'1'];
////
                }
//
//

                $counter++;
            }

            if(!empty($arr)){
               Product::insert($arr);
            }
        }

        return back()->with('success', 'Insert Record successfully.');
    }
}
