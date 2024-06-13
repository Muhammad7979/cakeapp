<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoleRequest;
use App\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Session;

class AdminRolesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
//        if (Gate::allows('view-role')) {
            $roles = Role::paginate(10);
            return view('admin.roles.index', compact('roles'));
//        } else {
//            return redirect('admin');
//        }
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
//        if (Gate::allows('create-role')) {
            return view('admin.roles.create');


//        } else {
//            return redirect('admin');
//        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRoleRequest $request)
    {
        //
//        if (Gate::allows('create-role')) {
            $input = $request->all();

            $arrayPermission = $request->input('permissions');
            foreach ($arrayPermission as $k => $b) {
                $arrayP[$b] = true;


            }


            $input['permissions'] = $arrayP;


            print_r($input['permissions']);

            $role = new Role();

//            $role->name = $input['name'];
//            $role->slug = $input['slug'];
//            $role->permissions = $input['permissions'];
//            $role->save();
            $role =      Role::firstOrCreate(['slug'=>$input['slug']],$input);

            if($role->wasRecentlyCreated)
            {
                Session::flash('created_role', 'Role Created');
                return redirect('admin/roles');
            }
            else
                {
                    Session::flash('created_role', 'Role Already Exists');
                    return redirect('admin/roles');
                }


//        } else {
//            return redirect('admin');
//        }
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
//        if (Gate::allows('update-role')) {
            $role = Role::findOrFail($id);
            return view('admin.roles.edit', compact('role'));
//        } else {
//            return redirect('admin');
//        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreRoleRequest $request, $id)
    {
        //
//        if (Gate::allows('update-role')) {
            $input = $request->all();

            $arrayPermission = $request->input('permissions');
            foreach ($arrayPermission as $k => $b) {
                $arrayP[$b] = true;


            }


            $input['permissions'] = $arrayP;


//        $role = new Role();
//
//        $role->name = $input['name'];
//        $role->slug= $input['slug'];
//        $role->permissions= $input['permissions'];
//        $role->save();
            Role::findOrFail($id)->update($input);
            Session::flash('updated_role', 'Role updated');
            return redirect('admin/roles');
//        } else {
//            return redirect('admin');
//        }
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
//        if (Gate::allows('delete-role')) {
            $role = Role::findOrFail($id);

            $role->delete();

            Session::flash('deleted_role', 'Role deleted');

            return redirect('admin/roles');
//        } else {
//            return redirect('admin');
//        }
    }
}
