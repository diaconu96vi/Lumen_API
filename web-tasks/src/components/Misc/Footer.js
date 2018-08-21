import React, {Component} from 'react';

export default class Footer extends Component {
    render() {
        const style = {
            backgroundColor: "#343a40",
            color: "rgba(255,255,255,.5)",
            borderTop: "1px solid",
            textAlign: "center",
            padding: "20px",
            position: "fixed",
            left: "0",
            bottom: "0",
            height: "60px",
            width: "100%",
        }
        return (
            <div className={'footer'}>
                <div style={style}>
                    practica roweb 2018 - diaconu ionut-victor;
                </div>
            </div>
        );
    }
}