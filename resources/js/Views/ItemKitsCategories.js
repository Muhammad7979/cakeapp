import React, { Component } from 'react';
import { Link } from 'react-router-dom'
import Footer from '../components/Footer';
import Loader from '../components/Loader';
import axios from 'axios';

export default class ItemKitsCategories extends Component {

    constructor() {
        super();
        
        this.state = {
            categories: [],
            loading: true
        }
    }

    componentDidMount() {
        this._fetchCategories();
    }
    
    _fetchCategories() {
        this.setState({ loading: true });
        axios.get('item_kits_categories').then(response => {
            console.log(response);
            this.setState({ categories: response.data, loading: false });
        });
    }

    render() {

        if (this.state.loading) {
            return <Loader/>;
        }
        
        return (

        <div>
          <div class="page-wrap">
            <header id="header">
              <nav>
                <ul>
                  <li><Link to={`/home`}>Main Menu</Link></li>
                  <li>»</li>
                  <li>Item kits</li>
                </ul>
              </nav>
              <div class="logo"> <a href="#"><img src="images/logo.png" alt=""/></a> </div>
            </header>
            <section id="body">
              <div class="inner_container">
                <div class="sub_categories">
                    <div class="title">
                    <h3><span>Please Select Items kit</span></h3>
                    </div>

                    <ul>
                        {
                            this.state.categories.map(category =>

                                <li key={category.item_kit_id}> <Link to={`/item_kit_order/${category.item_kit_id}`}>
                                     <img width="223px" height="210px" src={`images/item_kits/${category.image}`} alt=""/>
                                      {category.name} </Link> </li>
                            )
                        }
                    </ul>
                </div>
              </div>
            </section>
          </div>  
          <Footer />
        </div>
      );
    }
}