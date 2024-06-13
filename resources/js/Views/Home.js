import React, { Component } from 'react';
import { Link } from 'react-router-dom'
import Footer from '../components/Footer';
import Marquee from "react-smooth-marquee"

export default class Home extends Component {

  render() {
    return (

      <div className="body">
        <div className="page-wrap">

          <header id="header">
            <div className="logo"> <img src="images/logo.png" alt="" /> </div>
            <h1 className="ribbon">
              <strong className="ribbon-content">

                <div style={{ display: 'block-inline', width: '100%', height: '50px', overflow: 'hidden' }}>

                  <Marquee><span>Welcome to <span>Tehzeeb Bakers</span></span></Marquee>
                </div>

              </strong>
            </h1>
          </header>
          <section id="body">
            <div className="inner_container">
              <div className="main_categories">
                <ul>
                  <li className="events"><p>
                    <Link to={`/categories`}><b style={{ fontSize: 20 }}>Events</b></Link>
                  </p></li>

                  <li className="custom_order">
                    <Link to={`/place_order/0`}><b style={{ fontSize: 20 }}>Custom Order</b></Link>
                  </li>
                  <li className="custom_order">
                    <Link to={`/pos_order/0`}><b style={{ fontSize: 20 }}>Items</b></Link>
                  </li>
                  {/* <li className="custom_order">
                    <Link to={`/pos_order/items`}><b style={{ fontSize: 20 }}>Items</b></Link>
                  </li> */}
                  <li className="custom_order">
                    <Link to={`/item_kits`}><b style={{ fontSize: 20 }}>Item Kits</b></Link>
                  </li>
                  <li className="custom_order">
                    <Link to={`/custom_item_kit_order/`}><b style={{ fontSize: 20 }}>Custom Item Kits</b></Link>
                  </li>
                  {/* <li className="custom_order">
                    <Link to={`/mix_order/mix`}><b style={{ fontSize: 20 }}>Mix Items Order</b></Link>
                  </li> */}
                  {/* <li className="custom_order">
                    <Link to={`/custom_box`}><b style={{ fontSize: 20 }}>Custom Box</b></Link>
                  </li>
                  <li className="custom_order">
                    <Link to={`/mix_order/mix`}><b style={{ fontSize: 20 }}>Custom Box</b></Link>
                  </li> */}
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