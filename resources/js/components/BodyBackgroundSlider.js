import React, { Component } from 'react';
let intervalId=null;
export default class BGImage extends Component {
  constructor() {
    super()
    this.state = {
      opacity: 1,
      leftUri: "url('/assets/images/staticImage/fixed_background.png')",
      rightUri:"url('/assets/images/staticImage/fixed_background.png')",
      left:'100%',
      right:'0%',
      changedLeft:false,
    }
    this.timer=this.timer.bind(this);
  }

  componentDidMount() {    
    intervalId = setInterval(this.timer, 120000);
  }

  componentWillUnmount() {
    clearInterval(intervalId);
  }

  _animate(changeLeft){

    if(changeLeft)
    this.setState({left:'0%'})
    else
    this.setState({left:'100%'})

  }

  timer() {
    var rand = Math.trunc(1 + (Math.random() * (5)))
    let changeLeft= this.state.changedLeft;
    let change=(changeLeft)?('leftUri'):('rightUri')

    switch (rand) {
      case 1:
        {
          this.setState({[change]:"url('/assets/images/staticImage/bg-1.jpg')",changedLeft:(changeLeft)?false:true},()=>this._animate(changeLeft))
          break
        }
        case 2:
        {
          this.setState({[change]:"url('/assets/images/staticImage/bg-4.jpg')",changedLeft:(changeLeft)?false:true},()=>this._animate(changeLeft))
          break
        }
        case 3:
        {
          this.setState({[change]:"url('/assets/images/staticImage/bg-2.jpg')",changedLeft:(changeLeft)?false:true},()=>this._animate(changeLeft))
          break
        }
        case 4:
        {
          this.setState({[change]:"url('/assets/images/staticImage/fixed_background.png')",changedLeft:(changeLeft)?false:true},()=>this._animate(changeLeft))
          break
        }
        case 5:
        {
          this.setState({[change]:"url('/assets/images/staticImage/bg-3.jpg')",changedLeft:(changeLeft)?false:true},()=>this._animate(changeLeft))
          break
        }

        default:
            this.setState({[change]:"url('/assets/images/staticImage/fixed_background.png')",changedLeft:(changeLeft)?false:true},()=>this._animate(changeLeft))
        break
    }
  }

  render() {
    return (
      <div>
      <div
      className='background_slider'
        style={{
          opacity: 1,
          backgroundImage: this.state.leftUri,
          position:"absolute",
          top:'0',left:'0',right:'0',bottom:'0',zIndex:'-2',
          height: '100%', 
          width:'100%',
        }}
      />
      <div
      className='background_slider'
        style={{
          opacity: 1,
          backgroundImage: this.state.rightUri,
          position:"absolute",
          top:'0',left:'0',right:'0',bottom:'0',zIndex:'-2',
          height: '100%', 
          width:this.state.left,
        }}
      />
      </div>
    );
  }
}