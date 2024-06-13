<html>
<head>
    {{--<link href="{{asset('css/invoice.css')}}" rel="stylesheet">--}}
    <link href="{{asset('css/bootstrap.css')}}" rel="stylesheet">

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>INVOICE</title>

</head>

<body>
<div class="container">
    <div class="row">
    <div class="header-invoice text-center">
        <?php $logo = '/images/logo_tehzeeb.png' ?>
        <img class="img-circle"  height="300" src="/images/logo_tehzeeb.png">
    </div>
    </div>

    <div class="row">
            <h1 class="text-center">Customer Invoice</h1>
    </div>

    <div class="row" style="margin-top: 6%;">
          <div class= "order-information col-md-12">
            <div class="product-image col-md-4">

    @if($order->is_custom==1)
        <td><img height="300" src="{{$order->photo ? URL::asset('/images/Custom_Orders/'.$order->photo_path) : '/images/avatar-male.jpg' }}" alt="" > </td>
    @else
        <td><img height="300" src="{{$order->photo ?  URL::asset('images/Product_Images/'.$order->photo_path ): '/images/avatar-male.jpg' }}" alt="" > </td>
    @endif

                    </div>



        <div class="customer-details col-md-4 ">
                            <div class="form-group" >
                      <span>  <h3 style="display: inline ;
                    font-family: 'Open Sans', sans-serif;
                    font-size: 25px;
                    line-height: 28px; font-weight: bold "> Name </h3>
                        <p style="display: inline ;
                    font-family: 'Open Sans', sans-serif;
                    font-size: 20px;
                    line-height: 28px;  ">{{$order->customer_name}}</p>
                   </span>
                            </div>
                            <div class="form-group">
                     <span>  <h3 style="display: inline ;
                    font-family: 'Open Sans', sans-serif;
                    font-size: 25px;
                    line-height: 28px; font-weight: bold "> Phone Number </h3>
                        <p style="display: inline ;
                    font-family: 'Open Sans', sans-serif;
                    font-size: 20px;
                    line-height: 28px;  ">{{$order->customer_phone}}</p>
                   </span>
                            </div>
                            <div class="form-group">
                        <span>  <h3 style="display: inline ;
                    font-family: 'Open Sans', sans-serif;
                    font-size: 25px;
                    line-height: 28px; font-weight: bold "> Order Id </h3>
                        <p style="display: inline ;
                    font-family: 'Open Sans', sans-serif;
                    font-size: 20px;
                    line-height: 28px;  ">{{$order->order_number}}</p>
                        </span>
                            </div>

        </div>


        <div class="order-details">

            <div class="form-group" >
         <span>  <h3 style="display: inline ;
    font-family: 'Open Sans', sans-serif;
    font-size: 25px;
    line-height: 28px; font-weight: bold "> Tehzeeb Branch </h3>
        <p style="display: inline ;
    font-family: 'Open Sans', sans-serif;
    font-size: 20px;
    line-height: 28px;  ">{{$branchName->value}}</p>
         </span>
            </div>

            <div class="form-group" >
           <span>  <h3 style="display: inline ;
    font-family: 'Open Sans', sans-serif;
    font-size: 25px;
    line-height: 28px; font-weight: bold "> Branch Phone </h3>
        <p style="display: inline ;
    font-family: 'Open Sans', sans-serif;
    font-size: 20px;
    line-height: 28px;  ">{{$branchNumber->value}}</p>
           </span>
            </div>

            <div class="form-group">
     <span>  <h3 style="display: inline ;
    font-family: 'Open Sans', sans-serif;
    font-size: 25px;
    line-height: 28px; font-weight: bold "> Order Date </h3>
        <p style="display: inline ;
    font-family: 'Open Sans', sans-serif;
    font-size: 20px;
    line-height: 28px;  ">{{$order->created_at->format('F j, Y')}}</p>
   </span>
            </div>
            <div class="form-group">
        <span>  <h3 style="display: inline ;
    font-family: 'Open Sans', sans-serif;
    font-size: 25px;
    line-height: 28px; font-weight: bold ">Delivery Date</h3>
        <p style="display: inline ;
    font-family: 'Open Sans', sans-serif;
    font-size: 20px;
    line-height: 28px;  ">{{ Carbon\Carbon::parse($order->delivery_date)->format('Y-m-d')}}</p>
   </span>
            </div><div class="form-group" >
      <span>  <h3 style="display: inline ;
    font-family: 'Open Sans', sans-serif;
    font-size: 25px;
    line-height: 28px; font-weight: bold "> Delivery Time </h3>
        <p style="display: inline ;
    font-family: 'Open Sans', sans-serif;
    font-size: 20px;
    line-height: 28px;  ">{{$order->delivery_time}}</p>
   </span>
            </div>

            {{--<label> Tehzeeb <p>{{$branchName->value}} </p> </label>--}}
            {{--<label> Order Date <p>{{$order->created_at->format('j f y')}}</p> </label>--}}
            {{--<label> Delivery Date <p>{{$order->delivery_date}} </p> </label>--}}
            {{--<label> Delivery Time <p>{{$order->delivery_time}} </p> </label>--}}
        </div>




    </div>
    </div>


<div >
    <table class="table table-bordered ">
        <thead>
        <tr>

            <th style="font-size: 25px" scope="col">Product</th>
            <th style="font-size: 25px" scope="col">Flavours</th>
            <th style="font-size: 25px" scope="col">Materials</th>
            <th style="font-size: 25px" scope="col">Weight</th>
            <th style="font-size: 25px" scope="col">Quantity</th>
            <th style="font-size: 25px" scope="col">Total</th>
            <th style="font-size: 25px" scope="col">Advance</th>

            {{--<th scope="col">Handle</th>--}}
        </tr>
        </thead>
        <tbody>
        <tr>
            <td style="font-size: 25px">{{$order->product_name}}</td>
            <td style="font-size: 25px">
                @foreach ($flavours as $flavour)
                    {{"".$flavour->flavour_name." - ".$flavour->flavour_price." ,"}}
                @endforeach
            </td >
            <td style="font-size: 25px">   @foreach ($materials as $material)
                    {{"".$material->material_name." - ".$material->material_price." ,"}}
                @endforeach
            </td>
            <td style="font-size: 25px">{{$order->weight}}</td>
            <td style="font-size: 25px">{{$order->quantity}}</td>
            <td style="font-size: 25px">{{$order->total_price}}</td>
            <td style="font-size: 25px">{{$order->advance_price}}</td>
        </tr>
        </tbody>
    </table>
</div>
<div class="sales-information">

    <div class="form-group"  style="display: inline-block">
      <span>
           <p style=" padding-top: 3%;    padding-left: 3%;
    font-family: 'Open Sans', sans-serif;
    font-size: 20px;
    line-height: 28px;  ">{{$order->salesman}}</p>
          <h3 style="display: inline ;
    font-family: 'Open Sans', sans-serif;
    font-size: 25px;
    line-height: 28px; font-weight: bold ; text-decoration: underline">Salesman</h3>

   </span>
    </div>



    <div class="form-group"  style="display: inline-block; padding-left: 10%">
      <span>
           <p style=" padding-top: 3%;    padding-left: 0%;
    font-family: 'Open Sans', sans-serif;
    font-size: 20px;
    line-height: 28px;  ">{{$order->remarks}}</p>
          <h3 style="display: inline ;
    font-family: 'Open Sans', sans-serif;
    font-size: 25px;
    line-height: 28px; font-weight: bold ; text-decoration: underline">Remarks</h3>

   </span>
    </div>

    <div class="form-group" style="display: inline-block ; padding-left:10% ; padding-top: 20%"  >
      <span>  <h3 style="display: inline ;
    font-family: 'Open Sans', sans-serif;
    font-size: 25px;
    line-height: 28px; font-weight: bold "> Discount (Rs) : </h3>
        <p style="display: inline ;
    font-family: 'Open Sans', sans-serif;
    font-size: 25px;
    line-height: 28px;  ">{{$order->discount}}</p>
   </span>
    </div>


    <div class="form-group" style="display: inline-block ; padding-left:10% ; padding-top: 18%"  >
      <span>  <h3 style="display: inline ;
    font-family: 'Open Sans', sans-serif;
    font-size: 25px;
    line-height: 28px; font-weight: bold "> Balance (Rs) : </h3>
        <p style="display: inline ;
    font-family: 'Open Sans', sans-serif;
    font-size: 25px;
    line-height: 28px;  ">{{$order->total_price - $order->advance_price-$order->discount}}</p>
   </span>
    </div>


</div>


</div>
<div class="page-break">
</div>
<div class="container">
    <div class="header-invoice text-center">
        <?php $logo = '/images/logo_tehzeeb.png' ?>
        <img class="img-circle"  height="300" src="{{public_path().$logo}}">
    </div>
    <h2 class="text-center invoice-header ">Branch Invoice</h2>

    <div class="order-information">
        <div class="product-image">
            @if($order->is_custom==1)
                <img  height="200" src="{{public_path('images/Custom_Orders/').$order->photo_path}}">
            @else
                <img  height="200" src="{{public_path('images/Product_Images/').$order->photo_path}}">
            @endif


        </div>




        <div class="customer-details" >
            <div class="form-group" >
      <span>  <h3 style="display: inline ;
    font-family: 'Open Sans', sans-serif;
    font-size: 25px;
    line-height: 28px; font-weight: bold "> Name </h3>
        <p style="display: inline ;
    font-family: 'Open Sans', sans-serif;
    font-size: 20px;
    line-height: 28px;  ">{{$order->customer_name}}</p>
   </span>
            </div>
            <div class="form-group">
     <span>  <h3 style="display: inline ;
    font-family: 'Open Sans', sans-serif;
    font-size: 25px;
    line-height: 28px; font-weight: bold "> Phone Number </h3>
        <p style="display: inline ;
    font-family: 'Open Sans', sans-serif;
    font-size: 20px;
    line-height: 28px;  ">{{$order->customer_phone}}</p>
   </span>
            </div>
            <div class="form-group">
        <span>  <h3 style="display: inline ;
    font-family: 'Open Sans', sans-serif;
    font-size: 25px;
    line-height: 28px; font-weight: bold "> Order Id </h3>
        <p style="display: inline ;
    font-family: 'Open Sans', sans-serif;
    font-size: 20px;
    line-height: 28px;  ">{{$order->id}}</p>
   </span>
            </div>

        </div>
        <div class="order-details">

            <div class="form-group" >
      <span>  <h3 style="display: inline ;
    font-family: 'Open Sans', sans-serif;
    font-size: 25px;
    line-height: 28px; font-weight: bold "> Tehzeeb Branch </h3>
        <p style="display: inline ;
    font-family: 'Open Sans', sans-serif;
    font-size: 20px;
    line-height: 28px;  ">{{$branchName->value}}</p>
   </span>
            </div>
            <div class="form-group">
     <span>  <h3 style="display: inline ;
    font-family: 'Open Sans', sans-serif;
    font-size: 25px;
    line-height: 28px; font-weight: bold "> Order Date </h3>
        <p style="display: inline ;
    font-family: 'Open Sans', sans-serif;
    font-size: 20px;
    line-height: 28px;  ">{{$order->created_at->format('F j, Y')}}</p>
   </span>
            </div>
            <div class="form-group">
        <span>  <h3 style="display: inline ;
    font-family: 'Open Sans', sans-serif;
    font-size: 25px;
    line-height: 28px; font-weight: bold ">Delivery Date</h3>
        <p style="display: inline ;
    font-family: 'Open Sans', sans-serif;
    font-size: 20px;
    line-height: 28px;  ">{{ Carbon\Carbon::parse($order->delivery_date)->format('Y-m-d')}}</p>
   </span>
            </div><div class="form-group" >
      <span>  <h3 style="display: inline ;
    font-family: 'Open Sans', sans-serif;
    font-size: 25px;
    line-height: 28px; font-weight: bold "> Delivery Time </h3>
        <p style="display: inline ;
    font-family: 'Open Sans', sans-serif;
    font-size: 20px;
    line-height: 28px;  ">{{$order->delivery_time}}</p>
   </span>
            </div>

            {{--<label> Tehzeeb <p>{{$branchName->value}} </p> </label>--}}
            {{--<label> Order Date <p>{{$order->created_at->format('j f y')}}</p> </label>--}}
            {{--<label> Delivery Date <p>{{$order->delivery_date}} </p> </label>--}}
            {{--<label> Delivery Time <p>{{$order->delivery_time}} </p> </label>--}}
        </div>




    </div>


    <div >
        <table class="table table-bordered ">
            <thead>
            <tr>

                <th style="font-size: 25px" scope="col">Product</th>
                <th style="font-size: 25px" scope="col">Flavours</th>
                <th style="font-size: 25px" scope="col">Materials</th>
                <th style="font-size: 25px" scope="col">Weight</th>
                <th style="font-size: 25px" scope="col">Quantity</th>
                <th style="font-size: 25px" scope="col">Total</th>
                <th style="font-size: 25px" scope="col">Advance</th>

                {{--<th scope="col">Handle</th>--}}
            </tr>
            </thead>
            <tbody>
            <tr>
                <td style="font-size: 25px">{{$order->product_name}}</td>
                <td style="font-size: 25px">
                    @foreach ($flavours as $flavour)
                        {{"".$flavour->flavour_name." - ".$flavour->flavour_price." ,"}}
                    @endforeach
                </td >
                <td style="font-size: 25px"> @foreach ($materials as $material)
                        {{"".$material->material_name." - ".$material->material_price." ,"}}
                    @endforeach</td>
                <td style="font-size: 25px">{{$order->weight}}</td>
                <td style="font-size: 25px">{{$order->quantity}}</td>
                <td style="font-size: 25px">{{$order->total_price}}</td>
                <td style="font-size: 25px">{{$order->advance_price}}</td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="sales-information">

        <div class="form-group"  style="display: inline-block">
      <span>
           <p style=" padding-top: 3%;    padding-left: 3%;
    font-family: 'Open Sans', sans-serif;
    font-size: 20px;
    line-height: 28px;  ">{{$order->salesman}}</p>
          <h3 style="display: inline ;
    font-family: 'Open Sans', sans-serif;
    font-size: 25px;
    line-height: 28px; font-weight: bold ; text-decoration: underline">Salesman</h3>

   </span>
        </div>



        <div class="form-group"  style="display: inline-block; padding-left: 10%">
      <span>
           <p style=" padding-top: 3%;    padding-left: 0%;
    font-family: 'Open Sans', sans-serif;
    font-size: 20px;
    line-height: 28px;  ">{{$order->remarks}}</p>
          <h3 style="display: inline ;
    font-family: 'Open Sans', sans-serif;
    font-size: 25px;
    line-height: 28px; font-weight: bold ; text-decoration: underline">Remarks</h3>

   </span>
        </div>
        <div class="form-group" style="display: inline-block ; padding-left:10% ; padding-top: 20%"  >
      <span>  <h3 style="display: inline ;
    font-family: 'Open Sans', sans-serif;
    font-size: 25px;
    line-height: 28px; font-weight: bold "> Discount (Rs) : </h3>
        <p style="display: inline ;
    font-family: 'Open Sans', sans-serif;
    font-size: 25px;
    line-height: 28px;  ">{{$order->discount}}</p>
   </span>
        </div>


        <div class="form-group" style="display: inline-block ; padding-left:10% ; padding-top: 15%"  >
      <span>  <h3 style="display: inline ;
    font-family: 'Open Sans', sans-serif;
    font-size: 25px;
    line-height: 28px; font-weight: bold "> Balance (Rs) : </h3>
        <p style="display: inline ;
    font-family: 'Open Sans', sans-serif;
    font-size: 25px;
    line-height: 28px;  ">{{$order->total_price - $order->advance_price-$order->discount}}</p>
   </span>
        </div>


    </div>


</div>
{{--<script type="text/javascript"> try { this.print();   document.location.href("{{route('orders.index')}}") ; window.close() } catch (e) { window.onload = window.print;window.close() } </script>--}}
</body>
</html>