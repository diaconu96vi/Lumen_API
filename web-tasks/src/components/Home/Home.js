import React, {Component, Fragment} from 'react';
import Header from "../Misc/Header";
import Footer from "../Misc/Footer";

export default class Home extends Component {
    render() {
        return (
            <Fragment>

                <Header/>
                <div className="Home">
                    <p>Hello, friends!</p>
                    <p>Welcome to our website.</p>
                </div>
                <Footer/>

            </Fragment>
        )
    }
}
