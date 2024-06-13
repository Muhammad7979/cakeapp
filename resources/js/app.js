
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes React and other helpers. It's a great starting point while
 * building robust, powerful web applications using React + Laravel.
 */

require('./bootstrap');

/**
 * Next, we will create a fresh React component instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

import { BrowserRouter, Switch, Route } from 'react-router-dom';
import React from 'react';
import { render } from 'react-dom';
import Home from './Views/Home'
import Categories from './Views/Categories';
import Products from './Views/Products';
import PlaceOrder from './Views/PlaceOrder';
import PrintOrder from './Views/PrintOrder';
import BackgroundSlider from './components/BodyBackgroundSlider';
import { Provider as AlertProvider } from 'react-alert'
import AlertTemplate from 'react-alert-template-basic'
import '../css/style.css';
import MixOrder from './Views/MixOrder';
import KitOrder from './Views/KitOrder';
import CustomKitOrder from './Views/CustomKitOrder';
import MixOrderItems from './Views/MixOrderItems';
import PosOrderItems from './Views/PosOrderItems';

import PosItemsOrder from './Views/PosItemsOrder';
import ItemKitsCategories from './Views/ItemKitsCategories';
import CustomLunchBox from './Views/CustomLunchBox';
import PosOrder from './Views/PosOrder';

// optional cofiguration
const options = {
  position: 'bottom center',
  timeout: 3000,
  offset: '90px',
  transition: 'fade'
}

const element = document.getElementById('react');

if (element) {

  render(

    <AlertProvider template={AlertTemplate} {...options}>
      <BackgroundSlider />
      <BrowserRouter>
        <Switch>
          <Route exact path={'/home'} component={Home} />
          <Route exact path={'/categories'} component={Categories} />
          <Route exact path={'/item_kits'} component={ItemKitsCategories} />
          <Route exact path={'/products/:id'} component={Products} />
          {/* <Route exact path={'/place_order/-1'} component={PlaceOrder} /> */}
          <Route exact path={'/place_order/:id'} component={MixOrder} />
          <Route exact path={'/pos_order/:id'} component={PosOrder} />
          <Route exact path={'/item_kit_order/:id'} component={KitOrder} />
          <Route exact path={'/custom_item_kit_order'} component={CustomKitOrder} />
          <Route exact path={'/mix_order/mix'} component={MixOrder} />
          <Route exact path={'/mix_order/items'} component={MixOrderItems} />
          <Route exact path={'/pos_items'} component={PosOrderItems} />
          <Route exact path={'/pos_order/items'} component={PosItemsOrder} />
          <Route exact path={'/custom_box'} component={CustomLunchBox} />
          <Route exact path={'/print_order/:id'} component={PrintOrder} />
        </Switch>
      </BrowserRouter>
    </AlertProvider>,
    element
  );
}
