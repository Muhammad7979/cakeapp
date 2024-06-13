import React, { Component } from 'react';
import { Link } from 'react-router-dom'
import Footer from '../components/Footer';
import Loader from '../components/Loader';
import axios from 'axios';
import Select from 'react-select';
import { DatePicker, TimePicker } from 'react-md';
import { withAlert } from 'react-alert';
import { withRouter } from 'react-router-dom';
import { isSet, map } from 'lodash';
const DEFAULT_DATE = new Date(); //today
const MONTHS_BEFORE = new Date(DEFAULT_DATE);
MONTHS_BEFORE.setMonth(DEFAULT_DATE.getMonth() - 0);

const ONE_YEAR_AFTER = new Date(DEFAULT_DATE);
ONE_YEAR_AFTER.setYear(DEFAULT_DATE.getFullYear() + 1);

var dateFormat = require('dateformat');

const FORMAT_OPTIONS = {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
};

export default withAlert(class CustomKitOrder extends Component {

    constructor() {
        super();
        this.state = {
            data: {},
            loading: true,
            itemKit: {},
            allItemKit:{},
            deliveryDate: dateFormat(new Date(), "yyyy-mm-dd"),
            deliveryTime: dateFormat(new Date(), "hh:MM TT"),
            orderType: null,
            paymentType: null,
            orderPriority: null,
            orderCatagory: null,
            selectedFlavours: [],
            selectedMaterials: [],
            selectedKitOptions: [],
            assignedTo: null,
            salesmanName: '',
            customerName: '',
            customerEmail: '',
            customerPhone: '',
            productRemarks: '',
            customerRemarks: '',
            productId: 0,
            productWeight: 0,
            productPrice: 0,
            productQuantity: 1,
            productAdvanced: 0,
            currentBranchIndex: 0,
            valid: true,
            customOrder: true,
            imageFile: null,
            imagePreviewUrl: null,
            categoryId: 0,
            minAdvance: 0,
            discount: 0,
            discountApplicable: false,
            cart_data: {},
        }

        this.handleDateChange = this.handleDateChange.bind(this);
        this.handleTimeChange = this.handleTimeChange.bind(this);
        this._submitForm = this._submitForm.bind(this);
        this.handleUserInput = this.handleUserInput.bind(this);
        // this._handleImageChange = this._handleImageChange.bind(this);
    }


    componentDidMount() {
        this._fetchProducts();
    }

    _fetchProducts() {
        this.setState({ loading: true });
        axios.get(`/custom_item_kits_items`).then(response => {
            let product = response.data;
            console.log('responce placeorder.js', product);
            // if (response.data.product_details.id > 0) {
                this.setState({
                    allItemKit:response.data.all_kits,
                    // itemKit:response.data.items,
                    data: response.data,
                    // categoryId: response.data.product_details.category_id,
                    minAdvance: response.data.min_advance.min_Advance,
                    productAdvanced: response.data.min_advance.min_Advance,
                    // productId: response.data.product_details.id,
                    // productWeight: response.data.product_details.weight,
                    // productPrice: response.data.product_details.price,
                    currentBranchIndex: response.data.branches.findIndex((branch) => (branch.is_current) ? true : false),
                    defaultOrderPriorityIndex: response.data.priorities.findIndex((priority) => (priority.label == 'Medium') ? true : false),
                    defaultPaymentTypeIndex: response.data.payment_type.findIndex((payment) => (payment.name == 'Cash') ? true : false),
                    defaultOrderTypeIndex: response.data.order_types.findIndex((order) => (order.name == 'In Shop') ? true : false),
                    customOrder: false,
                    loading: false,
                }, () => { this.setState({ orderType: this.orderType_options()[this.state.defaultOrderTypeIndex], paymentType: this.payment_options()[this.state.defaultPaymentTypeIndex], orderPriority: this.priotiry_options()[this.state.defaultOrderPriorityIndex], assignedTo: this.branch_options()[this.state.currentBranchIndex] }) });
            // } else {
            //     //if ID zero, its a custom order
            //     this.setState({
            //         data: response.data,
            //         productId: response.data.product_details.id,
            //         minAdvance: response.data.min_advance.min_Advance,
            //         productAdvanced: response.data.min_advance.min_Advance,
            //         // productWeight: response.data.product_details.weight,
            //         // productPrice: response.data.product_details.price,
            //         currentBranchIndex: response.data.branches.findIndex((branch) => (branch.is_current) ? true : false),
            //         defaultOrderPriorityIndex: response.data.priorities.findIndex((priority) => (priority.label == 'Medium') ? true : false),
            //         defaultPaymentTypeIndex: response.data.payment_type.findIndex((payment) => (payment.name == 'Cash') ? true : false),
            //         defaultOrderTypeIndex: response.data.order_types.findIndex((order) => (order.name == 'In Shop') ? true : false),
            //         loading: false,
            //     }, () => { this.setState({ orderType: this.orderType_options()[this.state.defaultOrderTypeIndex], paymentType: this.payment_options()[this.state.defaultPaymentTypeIndex], orderPriority: this.priotiry_options()[this.state.defaultOrderPriorityIndex], assignedTo: this.branch_options()[this.state.currentBranchIndex] }) });
            // }
        });
    }

 
    _calculatePrice() {
        const { allItemKit } = this.state;
        let totalPrice = 0;
    
        // Iterate over each item in itemKit and calculate the total price
        allItemKit.map(itemKit =>{
           
        itemKit.items.forEach(item => {
            
            if(item.isSelected == true){
                if(!itemKit.quantity){
                    itemKit.quantity=1;
                   }
            const itemTotal = item.unit_price * item.quantity * itemKit.quantity;
            totalPrice += itemTotal;
            }
        });
    })
    
        return totalPrice;
    }
    



    handleDateChange(deliveryDate) {

        this.setState({ deliveryDate: dateFormat(deliveryDate, "yyyy-mm-dd") });
    }

    handleTimeChange(deliveryTime) {

        this.setState({ deliveryTime });
    }


    

    handleSelectChange(selectedOptions) {
    console.log(selectedOptions);
    const { allItemKit } = this.state;
    
    // Iterate through all item kits
    const updatedItemKits = allItemKit.map(itemKit => {
        // Check if the item kit is selected in the current options
        const isSelected = selectedOptions.some(selected => selected.value === itemKit.item_kit_id);

        console.log(isSelected);
        // Update items inside the current item kit
        const updatedItems = itemKit.items.map(item => {
            return { ...item, isSelected: isSelected };
        });

        // Update the item kit with the updated items
        return { ...itemKit, items: updatedItems };
    });

    this.setState({
        selectedKitOptions: selectedOptions,
        allItemKit: updatedItemKits,
    });
}



handleCheckboxChange(itemId, itemKitId, isChecked) {
    this.setState(prevState => {
      const updatedItemKits = { ...prevState.allItemKit };
      // Iterate through all item kits  
      for (const key in updatedItemKits) {
        if (updatedItemKits.hasOwnProperty(key)) {
          const itemKit = updatedItemKits[key];
        //   // Update items inside the current item kit
          const updatedItems = itemKit.items.map(item => {
            if (item.item_id === itemId && item.item_kit_id === itemKitId) {
              return { ...item, isSelected: isChecked };
            }
            return item;
          });
        //   // Update the items in the current item kit
          itemKit.items = updatedItems;
        }
      }
      
      return updatedItemKits ;
    });
  }
  
  
  handleQuantityChange(itemId, itemKitId, newQuantity) {
    this.setState(prevState => {
      const updatedItemKits = { ...prevState.allItemKit };
      // Iterate through all item kits  
      for (const key in updatedItemKits) {
        if (updatedItemKits.hasOwnProperty(key)) {
          const itemKit = updatedItemKits[key];
  
          // Update items inside the current item kit
          const updatedItems = itemKit.items.map(item => {
            if (item.item_id === itemId && item.item_kit_id === itemKitId) {
              return { ...item, quantity: newQuantity }; // Ensure newQuantity is a valid number
            }
            return item;
          });
           
          // Update the items in the current item kit
          itemKit.items = updatedItems;
        }
      }
      return updatedItemKits ;
    });
  }




handleKitQuantityChange(itemKitId, newQuantity) {
    this.setState(prevState => {
        const updatedItemKits = { ...prevState.allItemKit };
        
        // Iterate through all item kits
        for (const key in updatedItemKits) {
            if (updatedItemKits.hasOwnProperty(key)) {
                const itemKit = updatedItemKits[key];
  
                // Check if the current item kit matches the provided itemKitId
                if (itemKit.item_kit_id === itemKitId) {
                    // Update the quantity of the current item kit
                    itemKit.quantity = newQuantity; // Ensure newQuantity is a valid number

                    // Update items inside the current item kit
                    const updatedItems = itemKit.items.map(item => {
                        if (item.item_kit_id === itemKitId) {
                            // Add or update the kit_quantity key in the items
                            return { ...item, kit_quantity: newQuantity };
                        }
                        return item;
                    });

                    // Update the items in the current item kit
                    itemKit.items = updatedItems;
                }
            }
        }

        return updatedItemKits;
    });
}

  

    handleUserInput(e) {
        const name = e.target.name;
        const value = e.target.value;
        //console.log('event.target::',e);
        //debugger
        this.setState({ [name]: value });
    }

    branch_options() {
        return this.state.data.branches.map(item => {
            if (item.is_current) {
                return { 'value': item.id, 'label': `${item.name} - Current`, 'id': item.id, 'code': item.code }
            }
            return { 'value': item.id, 'label': item.name, 'id': item.id, 'code': item.code }

        })
    }

    catagory_options() {
        return this.state.data.flavour_categories.map(item => {
            return { 'value': item.id, 'label': item.name, }
        })
    }

    priotiry_options() {
        return this.state.data.priorities.map(item => {
            return { 'value': item.value, 'label': item.label, }
        })
    }
    payment_options() {
        return this.state.data.payment_type.map(item => {
            return { 'value': item.id, 'label': item.name, }
        })
    }

    orderType_options() {
        return this.state.data.order_types.map(item => {
            return { 'value': item.id, 'label': item.name, }
        })
    }


    kits_options() {
        const { allItemKit } = this.state;
        return allItemKit.map(item => ({
          value: item.item_kit_id,
          label: `${item.name}`,
          data:item.items,
        }));
      }


    async _submitForm(event) {
        event.preventDefault();
        const {
            allItemKit,
            productId,
            orderType,
            paymentType,
            orderPriority,
            orderCatagory,
            selectedFlavours,
            selectedMaterials,
            assignedTo,
            salesmanName,
            customerName,
            customerEmail,
            customerPhone,
            productWeight,
            productPrice,
            productQuantity,
            productAdvanced,
            productRemarks,
            customerRemarks,
            customOrder,
            currentBranchIndex,
            // imageFile,
            deliveryDate,
            deliveryTime,
            discount
        } = this.state;




        // if (orderType == null || paymentType == null || orderPriority == null || orderCatagory == null || selectedFlavours.length == 0 || selectedMaterials.length == 0) {
            if (orderType == null || paymentType == null || orderPriority == null) {
            console.log('valid false')
            this.setState({ valid: false })
        }
        else {
            try {
                let data = new FormData();
                let _temp = "";
                data.append("customer_phone", customerPhone);
                // data.append("weight", productWeight);
                data.append("quantity", productQuantity);
                data.append("total_price", this._calculatePrice());
                data.append("advance_price", (productAdvanced) * 1);
                data.append("payment_type", paymentType.value);
                data.append("order_type", orderType.value);
                data.append("remarks", productRemarks);
                data.append("instructions", customerRemarks);
                data.append("assigned_to", assignedTo.code);
                
                data.append("priority", orderPriority.value);
                data.append("payment_status", 0);
                data.append("order_status", 'Un-Processed');
                data.append("delivery_date", deliveryDate);
                data.append("delivery_time", deliveryTime);
                data.append("branch_id", this.branch_options()[currentBranchIndex].id);
                data.append("branch_code", this.branch_options()[currentBranchIndex].code);
                data.append("salesman", salesmanName);
                data.append("customer_name", customerName);
                data.append("user_id", 0);
                data.append("discount", discount)
                data.append("is_active", 1);
                data.append("server_sync", 0);
                data.append("live_synced", 0);
                data.append("product_id", productId);
                // if (customOrder) {
                //     data.append("is_custom", 1);
                //     data.append("photo_id", 0);
                //     data.append("custom_image", imageFile);
                //     data.append("product_price", productPrice);
                // } else {
                    data.append("is_custom", 0);
                    // data.append("product_price", productPrice);
                    // data.append("photo_id", this.state.data.product_details.photo.id);
                // }
                // const selectedItems = this.state.itemKit.items.filter(item => item.isSelected === true );
                // const serializedItems = JSON.stringify(selectedItems);
const { allItemKit } = this.state;

// Flatten the items from allItemKit into a single array
const allItems = allItemKit.reduce((acc, itemKit) => [...acc, ...itemKit.items], []);

// Filter the selected items
const selectedItems = allItems.filter(item => item.isSelected === true);

// Serialize the selected items
const serializedItems = JSON.stringify(selectedItems);
                data.append("items", serializedItems);
                
                if(selectedItems.length == 0){
                    this.props.alert.error("Select atleast one item.");
                }else{

                let result = await axios.post('/customItemkitorder/create', data);
                // let result = await axios.post('/clientOrder/create', data);

                console.log('submitted::', result);
                if (result.status > 200) {
                    //server not connected or could not responde properly, internet err probably

                } else {
                    //server responded, check server message
                    if (result.data.status == "Success") {
                        //saved successfully

                        console.log("Success")

                        this.props.alert.success("Order has been created.")

                        this.props.history.push(`/print_order/${result.data.payLoad}`)

                    } else {
                        //failed to save for some reason
                        console.log("Error")

                        this.props.alert.error(result.data.statusMessage)
                    }
                }
              }
            } catch (e) {
                console.log('err axios post:', e);
            }
        }

    }



    _renderBredCrumb() {

        if (this.props.match.params.id != 0) {
            return (
                <header id="header" className="clearfix">
                    <nav className="inner_nav">
                        <ul>
                            <li><Link to={`/home`}>Main Menu</Link></li>
                            <li>»</li>
                            <li><Link to={`/item_kits`}>Item Kits</Link></li>
                            <li>»</li>
                            <li>Place Order</li>
                        </ul>
                    </nav>
                    <div className="logo"> <a href="#"><img src="/images/logo.png" alt="" /></a> </div>
                </header>
            );
        } else {
            return (
                <header id="header" className="clearfix">
                    <nav className="inner_nav">
                        <ul>
                            <li><Link to={`/home`}>Main Menu</Link></li>
                            <li>»</li>
                            <li>Place Order</li>
                        </ul>
                    </nav>
                    <div className="logo"> <a href="#"><img src="/images/logo.png" alt="" /></a> </div>
                </header>
            );
        }
    }




          renderDetails(selectedItem) {
            // Customize this method to render details based on the selected option
            const tableStyle = {
                borderCollapse: 'collapse',
                width: '100%',
              };
            
              const thTdStyle = {
                border: '1px solid #ddd',
                padding: '8px',
                textAlign: 'left',
              };
            
              return (
                <table style={tableStyle}>
                  <thead>
                    <tr>
                      <th style={thTdStyle}>Select</th>
                      <th style={thTdStyle}>Name</th>
                      <th style={thTdStyle}>Quantity</th>
                      <th style={thTdStyle}>Price</th>
                      {/* <th style={thTdStyle}>Item total</th> */}
                      {/* Add more table headers based on your item properties */}
                    </tr>
                  </thead>

                  <tbody>
                  {selectedItem.map((item,index) => (
                     
                      <tr key={index}>
                          <td style={thTdStyle}>
                           <input
                             type="checkbox"
                             defaultChecked={true}
                             onChange={(e) => this.handleCheckboxChange(item.item_id, item.item_kit_id, e.target.checked)}
                          />
                         </td>
                        <td style={thTdStyle}>{item.name}</td>
                        <td style={thTdStyle}>
                          <input
                            name='itemQuantity'
                            type="number"
                            min={1}
                            defaultValue={item.quantity}
                            onChange={(e) => this.handleQuantityChange(item.item_id, item.item_kit_id, e.target.value)}
                          />
                        </td>
                        <td style={thTdStyle}>{item.unit_price}</td>
                        {/* <td style={thTdStyle}>{item.unit_price * item.quantity}</td> */}
                        {/* Add more table cells based on your item properties */}
                      </tr>
                  ))}

                  </tbody>

                </table>
              );
          }
        
   


    render() {

        const {
            allItemKit,
            itemKit,
            orderDate,
            productId,
            orderType,
            paymentType,
            orderPriority,
            orderCatagory,
            selectedFlavours,
            selectedMaterials,
            selectedKitOptions,
            assignedTo,
            salesmanName,
            customerName,
            customerEmail,
            customerPhone,
            productWeight,
            productPrice,
            productQuantity,
            productAdvanced,
            productRemarks,
            customerRemarks,
            data,
            customOrder,
            imageFile,
            imagePreviewUrl,
            discount,
            discountApplicable
        } = this.state;
        //console.log('state object:', this.state);
        if (this.state.loading) {
            return <Loader />;
            //return <div />; loader make it seem like its taking long, remove ghoogoo.. 
        }
        return (
            <div className="inner_bg">
                <div id="wrapper">

                    {this._renderBredCrumb()}

                    <section id="body">
                        <div className="inner_container">
                            <div className="place_order_container clearfix">
                                <h3>Place Your Order Here</h3>
                                <form onSubmit={this._submitForm} ref={c => { this.form = c }}>
                                    {/* hidden fields */}
                                    <input name="productId" type="hidden" value={productId} />
                                    {/* fields of form with styling */}
                                    <div className="place_order_form">
                                        <div className="top_block clearfix">
                                            <div className="left_col">
                                              
                                                <div className="delivery_date_time">
                                                    <div className="title"> Delivery Date </div>
                                                    <div style={{ width: '65%', float: 'right', height: '38px' }}>
                                                        <div className="css-vj8t7z">
                                                            <DatePicker
                                                                id="deliveryDate"
                                                                autoOk
                                                                required
                                                                //inline
                                                                defaultValue={new Date()}
                                                                formatOptions={FORMAT_OPTIONS}
                                                                displayMode="portrait"
                                                                style={{ width: '98%', height: '38px' }}
                                                                minDate={MONTHS_BEFORE}
                                                                maxDate={ONE_YEAR_AFTER}
                                                                onChange={this.handleDateChange}
                                                            // placeholder="12/11/2018"
                                                            />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div className="delivery_date_time">
                                                    <div className="title"> Delivery Time </div>
                                                    <div style={{ width: '65%', float: 'right', height: '38px' }}>
                                                        <div className="css-vj8t7z" >
                                                            <TimePicker
                                                                id="deliveryTime"
                                                                //inline
                                                                locales="en-US"
                                                                defaultValue={new Date()}
                                                                required
                                                                displayMode="portrait"
                                                                style={{ width: '98%', height: '38px' }}
                                                                onChange={this.handleTimeChange}
                                                                hoverMode
                                                                autoOk
                                                            //placeholder="9:00 Am"
                                                            />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div className="delivery_date_time">
                                                    <div className="title"> Order Type </div>
                                                    <div className="fields">
                                                        <Select
                                                            value={orderType}
                                                            onChange={(orderType) => this.setState({ orderType })}
                                                            name="orderType"
                                                            options={this.orderType_options()}
                                                            placeholder={this.state.valid ? "Select..." : <span className="text-danger">Required</span>}
                                                        />
                                                    </div>
                                                </div>
                                                <div className="delivery_date_time">
                                                    <div className="title"> Payment Type </div>
                                                    <div className="fields">
                                                        <Select
                                                            value={paymentType}
                                                            onChange={(paymentType) => this.setState({ paymentType })}
                                                            name="paymentType"
                                                            options={this.payment_options()}
                                                            placeholder={this.state.valid ? "Select..." : <span className="text-danger">Required</span>}
                                                        />
                                                    </div>
                                                </div>
                                                <div className="delivery_date_time">
                                                    <div className="title"> Priority </div>
                                                    <div className="fields">
                                                        <Select
                                                            value={orderPriority}
                                                            onChange={(orderPriority) => this.setState({ orderPriority })}
                                                            name="orderPriority"
                                                            options={this.priotiry_options()}
                                                            placeholder={this.state.valid ? "Select..." : <span className="text-danger">Required</span>}
                                                        />
                                                    </div>
                                                </div>
                                                <div className="delivery_date_time">
                                                    <div className="title"> Add Kits </div>
                                                    <div className="fields">
                                                        <Select
                                                            value={selectedKitOptions}
                                                            onChange={(selectedOption) => this.handleSelectChange(selectedOption)}
                                                            isMulti
                                                            name="selectedKitOptions"
                                                            options={this.kits_options()}
                                                            placeholder={this.state.valid ? "Select..." : <span className="text-danger">Required</span>}
                                                        />
                                                    </div>
                                                </div>
  {selectedKitOptions && selectedKitOptions.length > 0 && (
  <div>
    {selectedKitOptions.map((selectedOption, index) => (
      <div className="delivery_date_time" key={index}>
        <div className="title">{selectedOption.label}
        </div>

        <div className="fields"> 
        <input
        name="additionalInput"
        type="number"
        defaultValue={1}
        min={1}
        onChange={(e) => this.handleKitQuantityChange(selectedOption.value, e.target.value)}
      /> </div>
       
        {/* Render details based on selected option */}
        <div className="field">
        {this.renderDetails(selectedOption.data)}
        </div>
      </div>
    ))}
  </div>
)}
                                              
                                             
                                            </div>
                                            <div className="right_col">
                                                <div className="name">
                                                    <div className="title"> Assigned To </div>
                                                    <div className="field">
                                                        <Select
                                                            //value={this.branch_options()[currentBranchIndex]}
                                                            value={assignedTo}
                                                            onChange={(assignedTo) => this.setState({ assignedTo })}
                                                            name="assignedTo"
                                                            options={this.branch_options()}
                                                        />
                                                    </div>
                                                </div>
                                                <div className="name">
                                                    <div className="title"> Salesman Name </div>
                                                    <div className="field">
                                                        <input autoComplete="none" name="salesmanName" type="text" onChange={(event) => this.handleUserInput(event)} value={salesmanName} required pattren={'^([a-zA-Z]+(([,. -][a-zA-Z ])?[a-zA-Z]*)*)$'} />
                                                    </div>
                                                </div>
                                                <div className="name">
                                                    <div className="title"> Customer Name </div>
                                                    <div className="field">
                                                        <input autoComplete="none" name="customerName" type="text" onChange={(event) => this.handleUserInput(event)} value={customerName} required />
                                                    </div>
                                                </div>
                                                <div className="name">
                                                    <div className="title"> Customer Email </div>
                                                    <div className="field">
                                                        <input autoComplete="none" name="customerEmail" type="email" onChange={(event) => this.handleUserInput(event)} value={customerEmail} pattern={'^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$'} />
                                                    </div>
                                                </div>
                                                <div className="name">
                                                    <div className="title"> Customer Phone </div>
                                                    <div className="field">
                                                        <input autoComplete="none" name="customerPhone" type="text" onChange={(event) => this.handleUserInput(event)} value={customerPhone} required />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div className="bottom_block">
                                            <div className="left_col">
                                                {/* <div className="ingredients">
                                                    <div className="title"> Weight </div>
                                                    <div className="field">
                                                        <input name="productWeight" type="text" onChange={(event) => this.handleUserInput(event)} value={productWeight} required pattern={'^(0*[1-9][0-9]*)$'} />
                                                    </div>
                                                </div> */}
                                                {/* <div className="ingredients">
                                                    <div className="title"> Price </div>
                                                    <div className="field">
                                                        <input name="productPrice" type="text" onChange={(event) => this.handleUserInput(event)} value={productPrice} required pattern={'^(0*[0-9][0-9]*)$'} />
                                                    </div>
                                                </div> */}
                                                <div className="ingredients">
                                                    <div className="title"> Quantity </div>
                                                    <div className="field">
                                                        <input name="productQuantity" type="number" onChange={(event) => this.handleUserInput(event)} value={productQuantity} required pattern={'^(0*[1-9][0-9]*)$'} />
                                                    </div>
                                                </div>
                                            </div>
                                            <div className="right_col">
                                                <div className="ingredients">
                                                    <div className="title"> Total (Rs) </div>
                                                    <div className="field">
                                                        <input name="total" type="text" value={this._calculatePrice()*productQuantity} />
                                                    </div>
                                                </div>
                                                {
                                                    (discountApplicable) ? (
                                                        <div className="ingredients">
                                                            <div className="title"> Discount (Rs) </div>
                                                            <div className="field">
                                                                <input name="discount" type="number" onChange={(event) => this.handleUserInput(event)} value={discount} required max={this._calculatePrice() - this.state.minAdvance} />
                                                            </div>
                                                        </div>
                                                    ) : (
                                                        <div>
                                                        </div>
                                                    )
                                                }
                                                <div className="ingredients">
                                                    <div className="title"> Advance (Rs) </div>
                                                    <div className="field">
                                                        <input name="productAdvanced" type="number" onChange={(event) => this.handleUserInput(event)} value={productAdvanced} required min={this.state.minAdvance} />
                                                    </div>
                                                </div>
                                                <div className="ingredients">
                                                    <div className="title"> Balance (Rs) </div>
                                                    <div className="field">
                                                        <input name="balance" type="text" value={this._calculatePrice()*productQuantity - productAdvanced * 1 - discount * 1} />
                                                    </div>
                                                </div>
                                            </div>
                                            <div className="remarks">
                                                <div className="title"> Message </div>
                                                <div className="text_area">
                                                    <textarea name="productRemarks" cols="" rows="5" onChange={(event) => this.handleUserInput(event)} value={productRemarks} />
                                                </div>
                                            </div>
                                            <div className="remarks">
                                                <div className="title"> Customer Remarks </div>
                                                <div className="text_area">
                                                    <textarea name="customerRemarks" cols="" rows="5" onChange={(event) => this.handleUserInput(event)} value={customerRemarks} />
                                                </div>
                                            </div>
                                            <div className="buttons">
                                                <input name="btnCancel" type="button" className="btn1" value="Cancel" onClick={() => {
                                                    (customOrder) ? (this.props.history.push("/home")) : (this.props.history.push(`/home`))
                                                }} />


                                                <input name="btnSubmit" type="submit" className="btn2" value="Order Now" />
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </section>
                </div>
                <Footer value={"inner_footer"} />
            </div>
        );
    }
});