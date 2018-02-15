import React from 'react';
import Top from './Top';
import Left from './Left';
import Major from './Major';

import 'semantic-ui-css/semantic.min.css';

export default class extends React.Component {

    constructor() {
        super();
        this.state = {
            headerHeight: 70,
            sidebarWidth: 200,
            contentPad: 30
        }
    }

    render() {
        return (
            <div style={{width:"100%",height:"100%"}}>
                <div style={{width:"100%",height:this.state.headerHeight+"px",backgroundColor:"#997700"}}>
                    <Top />
                </div>
                <div style={{position:"absolute",top:this.state.headerHeight+"px",left:"0px",transition:"width 0.5s",width:this.state.sidebarWidth+"px",height:"calc( 100% - " + this.state.headerHeight + "px )",backgroundColor:"#9944DD"}}>
                    <Left />
                </div>
                <div style={{boxSizing:"border-box",padding:this.state.contentPad+"px",paddingLeft:(this.state.sidebarWidth+this.state.contentPad)+"px",height:"calc( 100% - " + this.state.headerHeight + "px )",overflow:"auto"}}>
                    <div style={{backgroundColor:"#FFDD44",textAlign:"center"}}>
                        <Major />
                    </div>
                </div>
            </div>
        );
    }

}
