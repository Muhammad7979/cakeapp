<?php

namespace App\Http\Controllers;

use App\Branch;
use App\Group;
use App\Http\Requests\StoreUserRequest;
use App\Photo;
use App\Role;
use App\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Session;

class AdminUsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        if(Gate::allows('view-user')) {
            $users = User::paginate(10);
            return view('admin.users.index', compact('users'));
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
//        if(Gate::allows('create-user')) {
            $roles = Role::pluck('name', 'id')->all();
            $groups = Group::pluck('name', 'id')->all();
            $branches = Branch::pluck('name', 'id')->all();

            return view('admin.users.create', compact('roles', 'groups', 'branches'));
//        }
//        else
//        {
//            return redirect('admin');
//        }

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserRequest $request)
    {
        //
//        if(Gate::allows('create-user')) {
            $input = $request->except(['group_id']);
            if ($file = $request->file('photo_id')) {

                $name = time() . $file->getClientOriginalName();

                $file->move('images/User_Images', $name);

                $photo = Photo::create(['path' => $name]);

                $input['photo_id'] = $photo->id;
            }
            $input['password'] = bcrypt($request->password);



            $user = User::firstOrCreate(['email'=>$input['email']],$input);

            if($user->wasRecentlyCreated)
            {
                $user->groups()->sync($request->input('group_id'));

                Session::flash('created_user', 'The user has been created');
                return redirect('admin/users');
            }
            else
            {
                Session::flash('created_user', 'The user already exists');
                return redirect('admin/users');
            }

//        }
//        else
//        {
//            return redirect('admin');
//        }

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
        if (Gate::allows('update-user')) {

            $user = User::findOrFail($id);
            $roles = Role::pluck('name', 'id')->all();
            $groups = Group::pluck('name', 'id')->all();
            $branches = Branch::pluck('name', 'id')->all();

            return view('admin.users.edit', compact('user', 'roles', 'groups', 'branches'));
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
    public function update(StoreUserRequest $request, $id)
    {
        //
        if (Gate::allows('update-user')) {
            $user = User::findOrFail($id);

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

            $input = $request->except(['group_id']);

            if ($file = $request->file('photo_id')) {


                $name = time() . $file->getClientOriginalName();
                $file->move('images/User_Images', $name);

                if ($user->photo_id == "" || $user->photo_id == null) {
                    $photo = Photo::create(['path' => $name]);

                    $input['photo_id'] = $photo->id;

                } else {

                    $photo = Photo::find($user->photo_id);
                    $photo->path = $name;
                    $photo->save();
//                $elseif = $user->photo_id;
                    $input['photo_id'] = $photo->id;

                }

            }
            $input['password'] = bcrypt($request->password);


            $user->update($input);
            $user->groups()->sync($request->input('group_id'));

            Session::flash('updated_user', 'The user has been updated');
            return redirect('admin/users');
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
        if (Gate::allows('delete-user')) {
            $user = User::findOrFail($id);

            unlink(public_path()."images/User_Images" . $user->photo->path);

            $user->delete();

            Session::flash('deleted_user', 'The user has been deleted');


            return redirect('admin/users');
        }
        else
        {

            return redirect('admin');
        }

    }

}
