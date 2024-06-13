<?php

namespace App\Http\Controllers;

use App\Group;
use App\Http\Requests\StoreGroupRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class AdminGroupsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        if (Gate::allows('view-group')) {
        $groups  = Group::paginate(10);
        return view('admin.groups.index',compact('groups'));
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
    public function store(StoreGroupRequest $request)
    {
        //
        if (Gate::allows('create-group')) {

            try {


                $group = Group::firstOrCreate(['name' => $request->input('name')], $request->all());

                if ($group->wasRecentlyCreated) {
                    Session::flash('created_group', 'Group Created');
                    return redirect()->back();
                } else {
                    Session::flash('created_group', 'Group already exists');
                    return redirect()->back();
                }
            }catch (\Exception $exception)
            {
                Log::error("Fail to Create Group".$exception->getMessage());
                return back()->withError("Fail To Create Group");
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
        if (Gate::allows('update-group')) {
            try {
                $group = Group::findOrFail($id);
                return view('admin.groups.edit',compact('group'));

                }
            catch (ModelNotFoundException $exception)
                 {
                return back()->withError(" Group Not Found By ID".$id);
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
    public function update(StoreGroupRequest $request, $id)
    {
        //
        if (Gate::allows('update-group')) {
            try {
                $input = $request->all();
                Group::findOrFail($id)->update($input);
                Session::flash('updated_group', 'Group updated');
                return redirect('admin/groups');
            }catch (\Exception $exception)
            {
                Log::error("Fail to Update Group".$exception->getMessage());
                return back()->withError("Fail To Update Group");

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
        if (Gate::allows('delete-group')) {

            try {
                $group = Group::findOrFail($id);
                $group->delete();
                Session::flash('deleted_group', 'Group deleted');
                return redirect('admin/groups');
            }catch (\Exception $exception)
            {
                Log::error("Fail to Delete Group".$exception->getMessage());
                return back()->withError("Fail To Delete Group");
            }

        } else {
            return redirect('admin');
        }
    }
}
