<?php

namespace App\Http\Controllers;

use App\Branch;
use App\Configuration;
use App\Flavour;
use App\FlavourCategory;
use App\Material;
use App\OrderType;
use App\PaymentType;
use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientProductController extends Controller
{
    //
    public  function showCategoryProducts($id)
    {


        $products = DB::table('products')
            ->join('photos','products.photo_id','=','photos.id')
            ->where([['products.category_id','=',$id],['products.is_active','=','1']])
            ->select('products.id','products.name','products.weight','products.price','photos.path')
            ->get();

                /*
                 * Fetches all the products that are active and belongs to a category (specified through category_id)..
                 */

        return  response()->json($products);

    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProductData($id)
    {


        $product = Product::with('photo')->where('id','=',$id)->first();


            $branches = Branch::where('is_active', '=', '1')->get();

            if($branches->count()<0)
            {
                $data = ["status" => "Error","statusMessage"=>"No Branches In Database "];
                return response()->json($data);
            }
            $branchCode = Configuration::where('key', '=', 'branch_Code')->first();

            if($branchCode->count()<0)
            {
                $data = ["status" => "Error","statusMessage"=>"Branch Code Not Set In Configuration"];
                return response()->json($data);
            }

            if($branches->count()>0) {
                foreach ($branches as $branch) {
                    if ($branch->code == $branchCode->value) {
                        $branch['is_current'] = true;
                    } else {
                        $branch['is_current'] = false;
                    }
                }
            }

        $flavourCategories = FlavourCategory::all();
        $flavours = Flavour::all();
        $materials= Material::all();

        if($flavours->count()<0)
        {
            $data = ["status" => "Error","statusMessage"=>"No Flavours In database"];
            return response()->json($data) ;
        }
        if($materials->count()<0)
        {
            $data = ["status" => "Error","statusMessage"=>"No Materials In database"];
            return response()->json($data) ;
        }
        if($flavourCategories->count()<0)
        {
            $data = ["status" => "Error","statusMessage"=>"No Materials In database"];
            return response()->json($data) ;
        }

        $priorities = Configuration::where('key','=','Priority_key')->get();
        if($priorities->count()<0)
        {
            $data = ["status" => "Error","statusMessage"=>"Priorities Not Set In Configuration"];
            return response()->json($data);
        }

        $paymentTypes = PaymentType::all();
        $orderTypes = OrderType::all();

        $minAdvancePayment = Configuration::where('key','=','min_advance')->first();
        if(empty($minAdvancePayment))
        {
            $data = ["status" => "Error","statusMessage"=>"Minimum Advance Not Set In Configuration"];
            return response()->json($data);
        }else {
            $minAdvance = ['min_Advance' => $minAdvancePayment->value];
        }

        if (!$product) {

//            $flavours = Flavour::all();
//            $materials= Material::all();
            
            $product = ["id" => 0, "flavours" => $flavours, "materials" => $materials];
        }

        $product['flavours'] = $flavours;
        $product['materials'] = $materials;
        $data = ["product_details" => $product, "branches" => $branches,"flavour_categories"=>$flavourCategories,"priorities"=>$priorities,"payment_type"=>$paymentTypes,"order_types"=>$orderTypes,"min_advance"=>$minAdvance];

        return response()->json($data);

    }


    public function getCustomOrderData()
    {
        $flavours = Flavour::all();
        $materials= Material::all();
        $flavourCategories = FlavourCategory::all();

        $priorities = Configuration::where('key','=','Priority_key')->get();
//        if($priorities->count()<0)
//        {
            $data = ["status" => "Error","statusMessage"=>"Priorities Not Set In Configuration"];
            return response()->json($data);
//        }


        $minAdvancePayment = Configuration::where('key','=','min_advance')->first();
        if(empty($minAdvancePayment))
        {
            $data = ["status" => "Error","statusMessage"=>"Minimum Advance Not Set In Configuration"];
            return response()->json($data);
        }else {
            $minAdvance = ['min_Advance' => $minAdvancePayment->value];
        }

        $paymentTypes = PaymentType::all();
        $orderTypes = OrderType::all();




        $branches = Branch::where('is_active','=','1')->get();

        if($branches->count()<0)
        {
            $data = ["status" => "Error","statusMessage"=>"No Branches In Database "];
            return response()->json($data);
        }


        $branchCode=Configuration::where('key','=','branch_Code')->first();

        if(!$branchCode)
        {
            $data = ["status" => "Error","statusMessage"=>"Branch Code Not Set In Configuration"];
            return response()->json($data);
        }


        if($branches->count()>0) {


        foreach ($branches as $branch)
        {
            if($branch->code == $branchCode->value)
            {
                $branch['is_current']= true;
            }
            else
            {
                $branch['is_current']= false;
            }
        }
        }



        $data = ["flavour_categories" => $flavourCategories,"flavours" => $flavours,"materials" => $materials, "branches" => $branches, "priorities" => $priorities,"payment_type"=>$paymentTypes,"order_types"=>$orderTypes,"min_advance"=>$minAdvance];


        return response()->json($data);
    }


}
