import React, { Component } from 'react';
import { Link } from 'react-router-dom'
import Footer from '../components/Footer';
import Loader from '../components/Loader';
import axios from 'axios';
import Select from 'react-select';
import { DatePicker, TimePicker } from 'react-md';
import { withAlert } from 'react-alert';
import { withRouter } from 'react-router-dom';
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

export default withAlert(class MixOrder extends Component {

    constructor() {
        super();
        this.state = {
            data: {},
            loading: true,
            deliveryDate: dateFormat(new Date(), "yyyy-mm-dd"),
            deliveryTime: dateFormat(new Date(), "hh:MM TT"),
            orderType: null,
            paymentType: null,
            orderPriority: null,
            orderCatagory: null,
            selectedFlavours: [],
            selectedMaterials: [],
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
            cart_data: {}
        }

        this.handleDateChange = this.handleDateChange.bind(this);
        this.handleTimeChange = this.handleTimeChange.bind(this);
        this._submitForm = this._submitForm.bind(this);
        this.handleUserInput = this.handleUserInput.bind(this);
        this._handleImageChange = this._handleImageChange.bind(this);
    }

    handleClick() {
        const {
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
            imageFile,
            deliveryDate,
            deliveryTime,
            discount
        } = this.state;
        var newdata = {};
        if (customOrder) {
            newdata = {
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
                imageFile,
                deliveryDate,
                deliveryTime,
                discount,
                is_custom: 1,
                photo_id: 0,
                custom_image: imageFile,
                product_price: productPrice
            }
        }
        else {
            newdata = {
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
                imageFile,
                deliveryDate,
                deliveryTime,
                discount,
                is_custom: 0,
                photo_id: this.state.data.product_details.photo.id,
                custom_image: imageFile,
            };
        }
        if (orderType == null || paymentType == null || orderPriority == null || orderCatagory == null || selectedFlavours.length == 0 || selectedMaterials.length == 0) {

            this.setState({ valid: false })
        }
        else {
            this.setState({ cart_data: newdata }, () => {
                // After updating the state, navigate to another component with updated data
                this.props.history.push({
                    pathname: '/mix_order/items',
                    state: { cartData: [this.state.cart_data] }
                });
            });
        }


    }

    componentDidMount() {
        this._fetchProducts();
    }

    _fetchProducts() {
        this.setState({ loading: true });
        axios.get(`/product/${this.props.match.params.id}`).then(response => {
            let product = response.data;
            console.log('responce placeorder.js', product);
            if (response.data.product_details.id > 0) {
                this.setState({
                    data: response.data,
                    categoryId: response.data.product_details.category_id,
                    minAdvance: response.data.min_advance.min_Advance,
                    productAdvanced: response.data.min_advance.min_Advance,
                    productId: response.data.product_details.id,
                    productWeight: response.data.product_details.weight,
                    productPrice: response.data.product_details.price,
                    currentBranchIndex: response.data.branches.findIndex((branch) => (branch.is_current) ? true : false),
                    defaultOrderPriorityIndex: response.data.priorities.findIndex((priority) => (priority.label == 'Medium') ? true : false),
                    defaultPaymentTypeIndex: response.data.payment_type.findIndex((payment) => (payment.name == 'Cash') ? true : false),
                    defaultOrderTypeIndex: response.data.order_types.findIndex((order) => (order.name == 'In Shop') ? true : false),
                    customOrder: false,
                    loading: false,
                }, () => { this.setState({ orderType: this.orderType_options()[this.state.defaultOrderTypeIndex], paymentType: this.payment_options()[this.state.defaultPaymentTypeIndex], orderPriority: this.priotiry_options()[this.state.defaultOrderPriorityIndex], assignedTo: this.branch_options()[this.state.currentBranchIndex] }) });
            } else {
                //if ID zero, its a custom order
                this.setState({
                    data: response.data,
                    productId: response.data.product_details.id,
                    minAdvance: response.data.min_advance.min_Advance,
                    productAdvanced: response.data.min_advance.min_Advance,
                    // productWeight: response.data.product_details.weight,
                    // productPrice: response.data.product_details.price,
                    currentBranchIndex: response.data.branches.findIndex((branch) => (branch.is_current) ? true : false),
                    defaultOrderPriorityIndex: response.data.priorities.findIndex((priority) => (priority.label == 'Medium') ? true : false),
                    defaultPaymentTypeIndex: response.data.payment_type.findIndex((payment) => (payment.name == 'Cash') ? true : false),
                    defaultOrderTypeIndex: response.data.order_types.findIndex((order) => (order.name == 'In Shop') ? true : false),
                    loading: false,
                }, () => { this.setState({ orderType: this.orderType_options()[this.state.defaultOrderTypeIndex], paymentType: this.payment_options()[this.state.defaultPaymentTypeIndex], orderPriority: this.priotiry_options()[this.state.defaultOrderPriorityIndex], assignedTo: this.branch_options()[this.state.currentBranchIndex] }) });
            }
        });
    }

    _calculatePrice() {
        const { productPrice, productWeight, productQuantity, selectedFlavours, selectedMaterials } = this.state
        let totalPrice = 0;
        let flavorPrice = 0;
        //(weight*[flavours]+weight[materials]+productPrice)*quantity
        //selectedFlavours.forEach((flavour) => { totalPrice = totalPrice + (productWeight * flavour.flavourPrice) });
        if (selectedFlavours.length > 0) {
            selectedFlavours.forEach((flavour) => { flavorPrice = flavorPrice + flavour.flavourPrice * 1 });
            flavorPrice = (flavorPrice / selectedFlavours.length) * productWeight;
        }
        //selectedMaterials.forEach((material) => { totalPrice = totalPrice + (productWeight * material.materialPrice) });
        totalPrice = (totalPrice + (productPrice * 1 + flavorPrice * 1)) * productQuantity; //1* productPrice is important to convert "productPrice" to int
        return totalPrice;
    }

    handleDateChange(deliveryDate) {

        this.setState({ deliveryDate: dateFormat(deliveryDate, "yyyy-mm-dd") });
    }

    handleTimeChange(deliveryTime) {

        this.setState({ deliveryTime });
    }

    _handleImageChange(e) {
        e.preventDefault();

        let reader = new FileReader();
        let file = e.target.files[0];
        console.log('file data', file);
        reader.onloadend = () => {
            this.setState({
                imageFile: file,
                imagePreviewUrl: reader.result
            });
        }
        reader.readAsDataURL(file)
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

    flavour_options() {
        if (this.state.orderCatagory) {
            let temp_flavours = []
            this.state.data.product_details.flavours.map(item => {
                if (item.flavourCategory_id == this.state.orderCatagory.value) {
                    temp_flavours.push({ 'value': item.id, 'label': `${item.name} - ${item.price}`, 'falvourId': item.id, 'flavourPrice': item.price })
                }
            });
            return temp_flavours
        } else {
            return []
        }
    }

    material_options() {
        return this.state.data.product_details.materials.map(item => {
            return { 'value': item.id, 'label': `${item.name} - ${item.price}`, 'materialId': item.id, 'materialPrice': item.price }
        })
    }



    async _submitForm(event) {
        event.preventDefault();
        const {
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
            imageFile,
            deliveryDate,
            deliveryTime,
            discount
        } = this.state;




        if (orderType == null || paymentType == null || orderPriority == null || orderCatagory == null || selectedFlavours.length == 0 || selectedMaterials.length == 0) {
            console.log('valid false')
            this.setState({ valid: false })
        }
        else {
            try {
                let data = new FormData();
                let _temp = "";
                data.append("customer_phone", customerPhone);
                data.append("weight", productWeight);
                data.append("quantity", productQuantity);
                data.append("total_price", this._calculatePrice());
                data.append("advance_price", (productAdvanced) * 1);
                data.append("payment_type", paymentType.value);
                data.append("order_type", orderType.value);
                data.append("remarks", productRemarks);
                data.append("instructions", customerRemarks);
                data.append("assigned_to", assignedTo.code);
                selectedFlavours.forEach((flavour) => _temp = _temp + `${flavour.falvourId},`);
                _temp = _temp.substring(0, _temp.length - 1);
                data.append("flavour_id", _temp);
                _temp = "";
                selectedMaterials.forEach((material) => _temp = _temp + `${material.materialId},`);
                _temp = _temp.substring(0, _temp.length - 1);
                data.append("material_id", _temp);
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
                if (customOrder) {
                    data.append("is_custom", 1);
                    data.append("photo_id", 0);
                    data.append("custom_image", imageFile);
                    data.append("product_price", productPrice);
                } else {
                    data.append("is_custom", 0);
                    data.append("product_price", productPrice);
                    data.append("photo_id", this.state.data.product_details.photo.id);
                }
                console.log(data)
                let result = await axios.post('/clientOrder/create', data);

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
                            <li><Link to={`/categories`}>Events</Link></li>
                            <li>»</li>
                            <li><Link to={`/products/${this.state.categoryId}`}>Products</Link></li>
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

    _renderImageView() {

        if (this.state.customOrder) {
            return (
                <div>
                    <input name="imageFile" type="file" accept="image/png, image/jpeg" onChange={(event) => this._handleImageChange(event)} style={{ borderRadius: 5, marginBottom: 5 }} />
                    <img src={this.state.imagePreviewUrl} alt="" />
                </div>
            );
        } else {
            return (
                <img src={`/images/Product_Images/${this.state.data.product_details.photo_path}`} alt="" />
            );
        }
    }

    render() {

        const {
            orderDate,
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
                                                <div className="cake_image">
                                                    <div className="title"> Cake Image </div>
                                                    <div className="thumbnail">
                                                        {this._renderImageView()}
                                                    </div>
                                                </div>
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
                                                    <div className="title"> Catagory </div>
                                                    <div className="fields">
                                                        <Select
                                                            //value={orderCatagory}
                                                            onChange={(orderCatagory) => this.setState({ orderCatagory })}
                                                            name="orderCatagory"
                                                            options={this.catagory_options()}
                                                            placeholder={this.state.valid ? "Select..." : <span className="text-danger">Required</span>}
                                                        />
                                                    </div>
                                                </div>
                                                <div className="delivery_date_time">
                                                    <div className="title"> Flavors </div>
                                                    <div className="fields">
                                                        <Select
                                                            //value={selectedFlavours}
                                                            onChange={(selectedFlavours) => this.setState({ selectedFlavours })}
                                                            isMulti
                                                            name="flavour_id"
                                                            options={this.flavour_options()}
                                                            placeholder={this.state.valid ? "Select..." : <span className="text-danger">Required</span>}
                                                        />
                                                    </div>
                                                </div>
                                                <div className="delivery_date_time">
                                                    <div className="title"> Materials </div>
                                                    <div className="fields">
                                                        <Select
                                                            //value={selectedMaterials}
                                                            onChange={(selectedMaterials) => this.setState({ selectedMaterials })}
                                                            isMulti
                                                            name="selectedMaterials"
                                                            options={this.material_options()}
                                                            placeholder={this.state.valid ? "Select..." : <span className="text-danger">Required</span>}
                                                        />
                                                    </div>
                                                </div>
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
                                                <div className="ingredients">
                                                    <div className="title"> Weight </div>
                                                    <div className="field">
                                                        <input name="productWeight" type="text" onChange={(event) => this.handleUserInput(event)} value={productWeight} required pattern={'^(0*[1-9][0-9]*)$'} />
                                                    </div>
                                                </div>
                                                <div className="ingredients">
                                                    <div className="title"> Price </div>
                                                    <div className="field">
                                                        <input name="productPrice" type="text" onChange={(event) => this.handleUserInput(event)} value={productPrice} required pattern={'^(0*[0-9][0-9]*)$'} />
                                                    </div>
                                                </div>
                                                <div className="ingredients">
                                                    <div className="title"> Quantity </div>
                                                    <div className="field">
                                                        <input name="productQuantity" type="text" disabled onChange={(event) => this.handleUserInput(event)} value={productQuantity} required pattern={'^(0*[1-9][0-9]*)$'} />
                                                    </div>
                                                </div>
                                            </div>
                                            <div className="right_col">
                                                <div className="ingredients">
                                                    <div className="title"> Total (Rs) </div>
                                                    <div className="field">
                                                        <input name="total" type="text" value={this._calculatePrice()} />
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
                                                        <input name="balance" type="text" value={this._calculatePrice() - productAdvanced * 1 - discount * 1} />
                                                    </div>
                                                </div>
                                            </div>
                                            <div className="remarks">
                                                <div className="title"> Cake Message </div>
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
                                                    (customOrder) ? (this.props.history.push("/home")) : (this.props.history.push(`/products/${this.state.categoryId}`))
                                                }} />

                                                {/* <Link to="/mix_order/items">
                                                    <button name="btnSubmit" type="submit" className="btn2">Add More Items</button>
                                                </Link> */}





                                                <input name="btnSubmit" type="submit" className="btn2" value="Order Now" />
                                                <button onClick={(event) => this.handleClick(event)} className="btn2">Add POS Items</button>
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