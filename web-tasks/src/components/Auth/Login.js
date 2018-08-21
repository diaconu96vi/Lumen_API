import React, {Component, Fragment} from 'react';
import axios from 'axios';
import Header from '../Misc/Header';
import Footer from '../Misc/Footer';

import { Button, Form, FormGroup, Label, Input, Col } from 'reactstrap';


export default class Login extends Component {
    state = {
        email: '',
        password: ''
    };

    _onChange = (e) => {
        const {name, value} = e.target;

        this.setState({
            [name]: value
        });
    };

    _login = async () => {
        const {email, password} = this.state;

        const response = await axios.post(process.env.REACT_APP_API_URL + 'login', {
            email, password
        });

        if (response && response.data && response.data.data) {
            sessionStorage.setItem('token', response.data.data.jwt);
            this.props.history.push('/users');
        } else {
            //afisam eroare
        }
    };

    _forgotPassword = () => {
        this.props.history.push('/forgot-password');
    }

    render() {
        const {email, password} = this.state;

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
                        <Label for="name" sm={2}>Email</Label>
                        <Col sm={4}>
                            <Input type={'text'} name={'email'} value={email} onChange={this._onChange}/>
                        </Col>
                    </FormGroup>
                    <FormGroup>
                        <Label for="password" sm={2}>Password</Label>
                        <Col sm={4}>
                            <Input type={"password"} name={"password"} value={password} onChange={this._onChange}/>
                        </Col>
                    </FormGroup>
                    <Button color="info" size="lg" onClick={this._login}>Login</Button>
                    <Button className="float-right" color="primary" size="lg" onClick={this._forgotPassword}>Forgot password</Button>
                </Form>
                <Footer/>
            </Fragment>
        )
    }
}
