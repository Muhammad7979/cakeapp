import React, { Component } from 'react';
import { Link } from 'react-router-dom'
import Footer from '../components/Footer';
import Loader from '../components/Loader';
import axios from 'axios';

export default class Categories extends Component {

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
        axios.get('/events').then(response => {
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
                  <li>Events</li>
                </ul>
              </nav>
              <div class="logo"> <a href="#"><img src="images/logo.png" alt=""/></a> </div>
            </header>
            <section id="body">
              <div class="inner_container">
                <div class="sub_categories">
                    <div class="title">
                    <h3><span>Please Select Sub Category</span></h3>
                    </div>

                    <ul>
                        {
                            this.state.categories.map(category =>

                                <li key={category.id}> <Link to={`/products/${category.id}`}> <img width="223px" height="210px" src={`images/Product_Categories/${category.path}`} alt=""/> {category.name} </Link> </li>
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