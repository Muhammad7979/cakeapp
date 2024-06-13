import React, { Component } from 'react';

export default class Footer extends Component {

    constructor(props) {
        super(props);
        this.state = {
          pendingSaleCount: null
        };
      }
    
      componentDidMount() {
        this.countSale();
      }

      countSale(){
        axios.get(`/pending_sale`)
          .then(response => {
            this.setState({ pendingSaleCount: response.data }); // Assuming response.data contains the pending sale count
          })
          .catch(error => {
            console.error('Error fetching pending sale count:', error);
          });
      };
    


    render() {
        const { pendingSaleCount } = this.state;
        return (
            <div className={this.props.value||''} style={footerStyle.footer}>
                <span style={footerStyle.footerSpan}>Cake software version 2.0.0. | {pendingSaleCount}</span>
            </div>
        );
    }
}

let footerStyle = {

    footer: {  
        width: '100%',
        background: '#be1238', 
        padding: 30, 
        position: 'fixed', 
        bottom: 0,
        left: 0,
        textAlign: 'center'
    },

    footerSpan: { 
        fontSize: 13, 
        fontWeight: 400, 
        color: '#d2c3c6'
    }
}