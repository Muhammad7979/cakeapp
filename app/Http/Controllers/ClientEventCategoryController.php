<?php

namespace App\Http\Controllers;

use App\Category;
use App\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientEventCategoryController extends Controller
{
    //
    /*
     * Function for Displaying Event/Cake Categories to User (CLIENT-Side)
     */

    public  function showCategories()
    {


        $categories = DB::table('categories')
                    ->join('photos','categories.photo_id','=','photos.id')
                    ->where([['categories.parent_id','!=','0'],['categories.is_active','=','1']])
                    ->select('categories.id','categories.name','photos.path')
                    ->get();
            /*
             * Above Query fetches the all the categories that are active and returns its id , name and image.name
             * which are stored in /images/{image name here}
             */


        return  response()->json($categories);

    }

}
