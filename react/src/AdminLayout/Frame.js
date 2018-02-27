import React from 'react';
import Top from './Top';
import Side from './Side';
import Major from './Major';
import { Menu, Icon, Segment, Sidebar } from 'semantic-ui-react'

import 'semantic-ui-css/semantic.min.css';
import './frame.css';

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
            <div className="frame-outer">
                <div className="frame-top">
                    <Top />
                </div>
                <div className="frame-page">
                    <Sidebar.Pushable>
                        <Sidebar direction="left" visible className="frame-side">
                            <Side />
                        </Sidebar>
                        <Sidebar.Pusher>
                            <Major />
                        </Sidebar.Pusher>
                    </Sidebar.Pushable>
                </div>
            </div>
        );
        /*
        return (
            <div className="frame-outer" style={{width:"100%",height:"100%"}}>
                <div className="frame-top" style={{width:"100%",height:this.state.headerHeight+"px",backgroundColor:"#997700"}}>
                    <Top />
                </div>
                <div className="frame-side" style={{position:"absolute",top:this.state.headerHeight+"px",left:"0px",transition:"width 0.5s",width:this.state.sidebarWidth+"px",height:"calc( 100% - " + this.state.headerHeight + "px )",backgroundColor:"#9944DD"}}>
                    <Side />
                </div>
                <div className="frame-major" style={{boxSizing:"border-box",padding:this.state.contentPad+"px",paddingLeft:(this.state.sidebarWidth+this.state.contentPad)+"px",height:"calc( 100% - " + this.state.headerHeight + "px )",overflow:"auto"}}>
                    <div style={{backgroundColor:"#FFDD44",textAlign:"center"}}>
                        <Major />
                    </div>
                </div>
            </div>
        );*/
    }

}
