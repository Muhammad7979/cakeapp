<?php

namespace App\Http\Controllers;

use App\Branch;
use App\Http\Requests\StoreBranchRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class AdminBranchesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        if (Gate::allows('view-branch')) {
        $branches  = Branch::paginate(5);
        return view('admin.branches.index',compact('branches'));
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
    public function store(StoreBranchRequest $request)
    {
        //
        if (Gate::allows('create-branch')) {
       $branch= Branch::firstOrCreate(['name'=>$request->input('name')],$request->all());
        if($branch->wasRecentlyCreated)
        {
            Session::flash('created_branch', 'Branch Added');
            return redirect()->back();
        }
        else
        {
            Session::flash('created_product', 'Branch Already Exists');
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
        if (Gate::allows('update-branch')) {
            try {
                $branch = Branch::findOrFail($id);
            }catch (ModelNotFoundException $exception)
            {
                return back()->withError("Branch Not Found by ID".$id);
            }

        return view('admin.branches.edit',compact('branch'));
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
    public function update(StoreBranchRequest$request, $id)
    {
        //
        if (Gate::allows('update-branch')) {
        $input = $request->all();
        try {
         $branch=    Branch::findOrFail($id);
        }catch (ModelNotFoundException $exception)
        {
            return back()->withError("Branch Not Found by ID".$id);
        }
        try {
            $branch->save($input);
        }catch (\Exception $exception)
        {    Log::error('Update Branch Error '.$exception->getMessage());
            return back()->withError("Unable to Update Branch");

        }
            Session::flash('updated_branch', 'Branch updated');
        return redirect('admin/branches');
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
        if (Gate::allows('delete-branch')) {
            try {
                $branch = Branch::findOrFail($id);
            }catch (ModelNotFoundException $exception)
            {
                return back()->withError("Trying to delete Branch That doesnt Exists Id".$id);
            }
            try {
                $branch->delete();
            }catch (\Exception $exception)

            {
                Log::error('Update Branch Error '.$exception->getMessage());
                return back()->withError("Unable to Delete Branch");

            }
        Session::flash('deleted_branch', 'Branch Deleted');
        return redirect('admin/branches');
        } else {
            return redirect('admin');
        }
    }

    public function getBranchData(Request $request)
    {
        $input=$request->input('name');
        if(isset($input)) {

            $data = Branch::where('code', '=', $request->input('name'))->get();


            if($data->count()>0) {

                return response()->json($data);
            }else
            {
                return back()->withError("No Branch Data");
            }
        }



    }

    public function  getBranches()
    {




        $branches= Branch::all();
        if($branches->count()>0) {
            return $branches;
        }else
        {
            return back()->withError("No Branches");
        }
    }

    public function  syncBranches()
    {

        $client = new \GuzzleHttp\Client(['base_uri' => env('Live_LINK'),'headers'=>['Accept'=>'application/json']]);
        try {
            $response = $client->request('POST', 'api/branchesLive/sync');
        } catch (GuzzleException $e) {

            Log::error('Error Syncing Branches'.$e->getMessage());

            return back()->withError("Error Syncing Branches");


        }


        $data =$response->getBody()->getContents();


        $decodedData= json_decode($data);


        try {
            foreach ($decodedData as $record) {


                $branch = Branch::updateOrCreate(['code' => $record->code],
                    ['name' => $record->name,
                        'code' => $record->code,
                        'address' => $record->address,
                        'phone' => $record->phone,
                        'is_active' => $record->is_active,
                        'created_at' => $record->created_at,
                        'updated_at' => $record->updated_at]);

            }
        }catch (\Exception $exception)
        {
            Log::error('Error Updating or Creating Branches'.$exception->getMessage());
            return back()->withError("Error updating Branches");
        }

        $query = Branch::where('code','!=','BH-000')->get();

            if($query->count()>0)
            {
                return $query;
            }
            else
            {
                return back()->withError("Error fetching Branches");
            }





    }

    public function getBranchesLive()
    {
            try {
                $branches = Branch::get(['name', 'code']);

                return response()->json($branches);
            }catch (\Exception $exception)
            {
                Log::error('Error getting live Branches'.$exception->getMessage());
                return back()->withError("Error Syncing Branches");
            }
    }
    public function syncBranchesLive()
    {

        try {
        $branches= Branch::where('code','!=','TH-000')->get();

        return response()->json($branches);
        }catch (\Exception $exception)
        {
            Log::error('Error getting live Branches'.$exception->getMessage());
            return back()->withError("Error Syncing Branches");
        }
    }


}
