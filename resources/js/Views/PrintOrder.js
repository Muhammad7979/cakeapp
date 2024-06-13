import React, { Component } from 'react';
import { Link } from 'react-router-dom'
import Loader from '../components/Loader';
import Footer from '../components/Footer';

export default class PrintOrder extends Component {

    constructor() {
        super();

        this.state = {
            customer_name: "",
            is_custom: 0,
            photo_path: '',
            product_name: '',
            weight: 0,
            total_price: 0,
            total: 0,
            pos_items_price: 0,
            loading: true
        }
    }

    componentDidMount() {

        console.log("print")
        this._fetchOrderDetails();
    }

    _fetchOrderDetails() {
        this.setState({ loading: true });
        axios.get(`/get/order/${this.props.match.params.id}`).then(response => {

            console.log(response.data);
            this.setState({
                customer_name: response.data.customer_name,
                is_custom: response.data.is_custom,
                photo_path: response.data.photo_path,
                product_name: response.data.product_name,
                weight: response.data.weight,
                total: response.data.total,
                pos_items_price: response.data.pos_item_total,
                total_price: response.data.total_price,
                custom_kit:response.data.custom_kit_name,

                loading: false
            });
        });
    }

    render() {

        if (this.state.loading) {
            return <Loader />;
        }

        return (

            <div className="wrapper">

                <header id="header" className="clearfix">
                    <nav>
                        <ul>
                            <li><Link to={`/home`}>Main Menu</Link></li>
                            <li>»</li>
                            <li>Print Order</li>
                        </ul>
                    </nav>
                    <div className="logo"> <a href="#"><img src="/images/logo.png" alt="" /></a> </div>
                </header>

                <div class="login_outer">
                    <div class="success_page_container">
                        <div class="title">
                            <h2>Thank You for Your Order</h2>
                            <span>{this.state.customer_name}</span>
                        </div>
                        <div class="product_detail">
                            <div class="product_image">
                                {(this.state.is_custom == 1) ?
                                    (<img width="200px" height="200px" src={`/images/Custom_Orders/${this.state.photo_path}`} alt="" />) :
                                    (<img width="200px" height="200px" src={`/images/Product_Images/${this.state.photo_path}`} alt="" />)}
                            </div>

                            <span>Your Order # : {this.props.match.params.id}</span>
                            {this.state.custom_kit !== null ? <h1>{this.state.custom_kit}</h1> : <h1>{this.state.product_name}</h1>}
                            {this.state.weight !== 0 && (<span> {this.state.weight}Pounds </span>) } <br></br>
                            {/* {this.state.pos_items_price !== null && (
                                <span>Cake total: {this.state.total_price} Rs | Other items total: {this.state.pos_items_price} Rs</span>
                            )} */}
                            {/* <span>Cake total:{this.state.total_price} .Rs | Other items total:{this.state.pos_items_price} .Rs</span> */}
                            <div class="price">
                                <span>Total Price:</span>  Rs. {this.state.total}
                            </div>

                            <div class="confirm_button">
                                <a href={`/generateInvoice/${this.props.match.params.id}`} target="_blank">Print</a>
                            </div>
                        </div>
                    </div>
                </div>

                <Footer />

            </div>
        );
    }
}