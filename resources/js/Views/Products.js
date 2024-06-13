import React, { Component } from 'react';
import { Link } from 'react-router-dom'
import Footer from '../components/Footer';
import Loader from '../components/Loader';
import axios from 'axios';
import _ from 'lodash';
import { AutoRotatingCarousel, Slide } from '../components/AutoRotatingCarousel';

let carouselIndex = 0;
export default class Products extends Component {

    constructor() {
        super();

        this.state = {
            products: [],
            category_id: '',
            loading: true,
            search: '',
            openCarousel: false,
        }
    }

    componentDidMount() {

        //console.log(this.props.match.params.id);

        this._fetchProducts();
    }

    _fetchProducts() {
        this.setState({ loading: true });
        axios.get(`/events/${this.props.match.params.id}/products`).then(response => {
            console.log(response);
            this.setState({ products: response.data, loading: false });
        });
    }

    _renderCarousel() {
        //console.log('carossss indx:', carouselIndex);
        //console.log("Product", this.state.products)

        return (
            <div style={{}}>
                <AutoRotatingCarousel
                    onRef={ref => (this._carousel = ref)}
                    label='Order'
                    buttonStyle={{ backgroundColor: '#a07aa7', color: 'white' }}
                    open={this.state.openCarousel}
                    autoplay={false}
                    onClose={() => this.setState({ openCarousel: false })}
                    style={{ position: 'absolute' }}
                    onChange={(index)=>{
                        carouselIndex=index;
                        console.log('current index in view:', carouselIndex);
                    }}
                    onStart={() => {
                        //console.log('button pressed::');
                        
                        this.props.history.push(`/place_order/${this.state.products[carouselIndex].id}`)
                    }}
                >
                    {this.state.products.map((product, index) => {
                        return (
                            <Slide
                                key={product.id}
                                media={<img src={`/images/Product_Images/${product.path}`} />}
                                mediaBackgroundStyle={{ backgroundColor: 'white' }}
                                style={{ backgroundColor: 'white' }}
                                title={`${product.name}`}
                                subtitle=''
                            />);
                    })}
                </AutoRotatingCarousel>
            </div>)
    }

    _updateSearch(event) {
        //this.props.history.push('/place_order/1');
        this.setState({ search: event.target.value.substr(0,20) });
    }

    render() {

        let filteredProducts = this.state.products.filter (
            (product) => {
                return product.name.toLowerCase().indexOf(this.state.search.toLowerCase()) !== -1;
            }
        );

        if (this.state.loading) {
            return <Loader />;
        }

        return (

            <div class="inner_bg">

                <div id="wrapper">
                    <header id="header" class="clearfix">
                        <nav class="inner_nav">
                            <ul>
                                <li><Link to={`/home`}>Main Menu</Link></li>
                                <li>»</li>
                                <li><Link to={`/categories`}>Events</Link></li>
                                <li>»</li>
                                <li>Products</li>
                            </ul>
                        </nav>
                        <div class="logo"> <a href="#"><img src="/images/logo.png" alt="" /></a> </div>
                        <div class="search_container">
                            <form action="" method="get">
                                <div class="search_block">
                                    <input value={this.state.search} onChange={this._updateSearch.bind(this)} type="text" class="text_field" placeholder="Search products here ..." />
                                    <input type="button" class="btn" />
                                </div>
                            </form>
                        </div>
                    </header>

                    <section id="body">
                        <div class="inner_container">

                        {this._renderCarousel()}

                            <div class="grid_view">
                                <div class="title">
                                    <h3><span>Please Select Product</span></h3>
                                </div>

                                <ul>
                                    {
                                        filteredProducts.map((product, index) =>

                                            <li key={product.id}>
                                                <img
                                                    width="282px"
                                                    height="211px"
                                                    src={`/images/Product_Images/${product.path}`}
                                                    alt=""
                                                    onClick={() => {
                                                        this._carousel.handleChange(index);
                                                        this.setState({ openCarousel: true })
                                                    }}
                                                />
                                                <div class="product_detail">
                                                    <h3>{product.name}</h3>
                                                    <div class="btn"><Link to={`/place_order/${product.id}`}>Order</Link> </div>
                                                </div>
                                            </li>
                                        )
                                    }
                                </ul>
                            </div>
                        </div>
                    </section>
                </div>

                <Footer value={"inner_footer"} />
            </div>
        );
    }
}
