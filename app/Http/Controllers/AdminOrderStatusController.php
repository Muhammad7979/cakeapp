<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGroupRequest;
use App\OrderStatus;
use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;

class AdminOrderStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        if (Gate::allows('view-orderStatus')) {
            $orderStatus = OrderStatus::paginate(10);
            return view('admin.orderStatus.index', compact('orderStatus'));
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
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreGroupRequest $request)
    {
        //
        if (Gate::allows('create-orderStatus')) {
            $orderStatus = OrderStatus::firstOrCreate(['name' => $request->input('name')], $request->all());

            if ($orderStatus->wasRecentlyCreated) {
                Session::flash('created_orderStatus', 'Status Created');
                return redirect()->back();
            } else {
                Session::flash('created_orderStatus', 'Status already exists');
                return redirect()->back();
            }
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
        if (Gate::allows('update-orderStatus')) {
            $orderStatus = OrderStatus::findOrFail($id);
            return view('admin.orderStatus.edit', compact('orderStatus'));
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
    public function update(StoreGroupRequest $request, $id)
    {
        //
        if (Gate::allows('update-orderStatus')) {
            $input = $request->all();
            OrderStatus::findOrFail($id)->update($input);
            Session::flash('updated_orderStatus', 'Status updated');
            return redirect('admin/orderStatuses');
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
        if (Gate::allows('delete-orderStatus')) {
            $orderStatus = OrderStatus::findOrFail($id);

            $orderStatus->delete();
            Session::flash('deleted_orderStatus', 'Status deleted');
            return redirect('admin/orderStatuses');
        } else {
            return redirect('admin');
        }
    }

}
