<?php

namespace App\Http\Controllers;

use App\Branch;
use App\Configuration;
use App\Http\Requests\StoreConfigRequest;
use App\Http\Requests\StoreSystemCofigRequest;
use App\PosSaleTemp;
use DemeterChain\C;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;

class SystemConfigurationsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        if (Gate::allows('view-configuration')) {
            $systemVariables  = Configuration::paginate(10);
            return view('admin.configurations.index',compact('systemVariables'));
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
        if (Configuration::where('key', '=', 'branch_code')->count()>0) {
            // user found
            $branchCode = Configuration::where('key', '=', 'branch_code')->first();
            $branchAddress = Configuration::where('key', '=', 'branch_address')->first();
            $branchName = Configuration::where('key', '=', 'branch_Name')->first();
            $website = Configuration::where('key', '=', 'website')->first();
            $systemId = Configuration::where('key', '=', 'system_id')->first();
            $branchNumber = Configuration::where('key', '=', 'branch_number')->first();
            $branchFax = Configuration::where('key', '=', 'branch_fax')->first();
            $returnPolicy = Configuration::where('key', '=', 'return_policy')->first();
            $branchId = Configuration::where('key', '=', 'branch_id')->first();

            return view('admin.configurations.system', compact('branchCode','branchAddress','branchName','website','systemId','branchNumber','branchFax','returnPolicy','branchId'));
        } //
        else {
            return view('admin.configurations.system');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreConfigRequest $request)
    {


        //
        if (Gate::allows('create-configuration')) {
            $systemVariable= Configuration::firstOrCreate(['value'=>$request->input('value'),'key'=>$request->input('key')],$request->all());

            if($systemVariable->wasRecentlyCreated) {
                Session::flash('created_configuration', 'Variable Created');
                return redirect()->back();
            }
            else
            {
                Session::flash('created_configuration', 'Variable already exists');
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
        if (Gate::allows('update-configuration')) {
            $systemVariable = Configuration::findOrFail($id);
            return view('admin.configurations.edit',compact('systemVariable'));
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
    public function update(Request $request, $id)
    {
        //
        if (Gate::allows('update-configuration')) {
            $input = $request->all();
            Configuration::findOrFail($id)->update($input);
            Session::flash('updated_configuration', 'Configuration updated');
            return redirect('admin/configurations');
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
        if (Gate::allows('delete-configuration')) {
            $systemVariable= Configuration::findOrFail($id);

            $systemVariable->delete();
            Session::flash('deleted_configuration', 'Type deleted');
            return redirect('admin/configurations');
        } else {
            return redirect('admin');
        }
    }

    public function getSystemConfiguration()
    {
   $branchCode=     Input::post('branchCode');
//        $client = new \GuzzleHttp\Client(['base_uri' => env('Live_LINK'),'headers'=>['Accept'=>'application/json']]);
//        try {
//            $response = $client->request('POST', 'api/branch/data', [
//                'form_params' => [
//                    'name' => $branchCode,
//                ]
//            ]);
//        } catch (GuzzleException $e) {
//            return $e;
//        }
        if(!empty($branchCode)) {

            $data = Branch::where('code', '=', $branchCode)->get();


            if($data->count()>0) {

                return $data;
            }else
            {
                return back()->withError("No Branch Data");
            }
        }








    }
    public function saveSystemConfiguration(StoreSystemCofigRequest $request)
    {

        if (Gate::allows('create-local-configuration')) {
            $systemConfig    = new  Configuration;
            $attributes=array_keys($request->toArray());

            foreach ($attributes as $key) {

                print_r($key);
//                $systemConfig->key = $key;
//                $systemConfig->value = $request->input($key);
//                $systemConfig->label = $request->input($key);
//                $systemConfig->save();

                    if($request->input($key)!=null) {
                        $systemConfig = Configuration::firstOrCreate(['key' => $key], ['key' => $key, 'value' => $request->input($key), 'label' => $request->input($key)]);
                        if ($systemConfig->wasRecentlyCreated) {

                            //return redirect()->back();
                        } else {
                            $setting = Configuration::where('key', '=', $key)->first();
                            $setting->key = $key;
                            $setting->value = $request->input($key);
                            $setting->label = $request->input($key);
                            $setting->save();
                            // return redirect()->back();
                        }
                    }
            }


                Session::flash('created_configuration', 'Variable Created');
                return redirect('admin');



        } else {
            return redirect('admin');
        }


        //  print_r($key .'' .$request->input($key));
       }

       public function pending_sale(){
        
        return PosSaleTemp::all()->count();

       }


}
