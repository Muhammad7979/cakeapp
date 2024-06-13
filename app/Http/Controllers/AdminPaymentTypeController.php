<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGroupRequest;
use App\PaymentType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Session;

class AdminPaymentTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        if (Gate::allows('view-paymentType')) {
            $paymentTypes  = PaymentType::paginate(10);
            return view('admin.paymentType.index',compact('paymentTypes'));
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
        if (Gate::allows('create-paymentType')) {
            $paymentType= PaymentType::firstOrCreate(['name'=>$request->input('name')],$request->all());

            if($paymentType->wasRecentlyCreated) {
                Session::flash('created_paymentType', 'Type Created');
                return redirect()->back();
            }
            else
            {
                Session::flash('created_paymentType', 'Type already exists');
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
        if (Gate::allows('update-paymentType')) {
            $paymentType = PaymentType::findOrFail($id);
            return view('admin.paymentType.edit',compact('paymentType'));
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
        if (Gate::allows('update-paymentType')) {
            $input = $request->all();
            PaymentType::findOrFail($id)->update($input);
            Session::flash('updated_paymentType', 'Type updated');
            return redirect('admin/paymentTypes');
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
        if (Gate::allows('delete-paymentType')) {
            $paymentType= PaymentType::findOrFail($id);

            $paymentType->delete();
            Session::flash('deleted_paymentType', 'Type deleted');
            return redirect('admin/paymentTypes');
        } else {
            return redirect('admin');
        }
    }
}
