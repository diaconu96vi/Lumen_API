import React, {Component, Fragment} from 'react';
import axios from 'axios';
import Header from '../Misc/Header';
import Footer from '../Misc/Footer';

import { Button, Form, FormGroup, Label, Input, Col } from 'reactstrap';


export default class ForgotPassword extends Component {
    state = {
        email: '',
    };

    _onChange = (e) => {
        const {name, value} = e.target;

        this.setState({
            [name]: value
        });
    };

    _forgotPassword = async () => {
        const {email} = this.state;

        await axios.post(process.env.REACT_APP_API_URL + 'forgot-password', {
            email
        });

        this.props.history.push('/change-password');

    };


    render() {
        const {email} = this.state;

        const style = {
            border: "2px solid grey",
            margin: "50px auto",
            width: "50%",
            backgroundColor: "#F0F8FF"
        }

        return (
            <Fragment>
                <Header/>
                <Form style={style}>
                    <FormGroup>
                        <Label for="email" sm={2}>Email</Label>
                        <Col sm={4}>
                            <Input type={"email"} name={"email"} value={email} onChange={this._onChange}/>
                        </Col>
                    </FormGroup>
                    <Button color="primary" size="lg" onClick={this._forgotPassword}>Reset password</Button>
                </Form>
                <Footer/>
            </Fragment>
        )
    }
}