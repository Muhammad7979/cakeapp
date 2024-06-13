<?php

namespace App\Http\Controllers;

use App\Group;
use App\Http\Requests\StoreGroupRequest;
use App\OrderType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Session;

class AdminOrderTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        if (Gate::allows('view-orderType')) {
            $orderTypes  = OrderType::paginate(10);
            return view('admin.orderType.index',compact('orderTypes'));
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
    public function store(Request $request)
    {
        //
        if (Gate::allows('create-orderType')) {
            $orderType= OrderType::firstOrCreate(['name'=>$request->input('name')],$request->all());

            if($orderType->wasRecentlyCreated) {
                Session::flash('created_orderType', 'Type Created');
                return redirect()->back();
            }
            else
            {
                Session::flash('created_orderType', 'Type already exists');
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
        if (Gate::allows('update-orderType')) {
            $orderType = OrderType::findOrFail($id);
            return view('admin.orderType.edit',compact('orderType'));
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
        if (Gate::allows('update-orderType')) {
            $input = $request->all();
            OrderType::findOrFail($id)->update($input);
            Session::flash('updated_orderType', 'Type updated');
            return redirect('admin/orderTypes');
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
        if (Gate::allows('delete-orderType')) {
            $orderType = OrderType::findOrFail($id);

            $orderType->delete();
            Session::flash('deleted_orderType', 'Type deleted');
            return redirect('admin/orderTypes');
        } else {
            return redirect('admin');
        }
    }
}
