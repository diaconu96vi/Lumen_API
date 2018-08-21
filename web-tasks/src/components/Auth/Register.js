import React, {Component, Fragment} from 'react';
import axios from 'axios';
import Header from '../Misc/Header';
import Footer from '../Misc/Footer';

import { Button, Form, FormGroup, Label, Input, Col } from 'reactstrap';

export default class Register extends Component {
    state = {
        name: '',
        email: '',
        password: ''
    };

    _onChange = (e) => {
        const {name, value} = e.target;

        this.setState({
            [name]: value
        });
    };

    _register = async () => {
        const {name, email, password} = this.state;

        await axios.post(process.env.REACT_APP_API_URL + 'register', {
            name, email, password
        });


        this.props.history.push('/login');
    };

    render() {
        const {name, email, password} = this.state;

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
                        <Label for="name" sm={2}>Name</Label>
                        <Col sm={4}>
                            <Input type={'text'} name={'name'} value={name} onChange={this._onChange}/>
                        </Col>
                    </FormGroup>
                    <FormGroup>
                        <Label for="email" sm={2}>Email</Label>
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
                    <Button color="info" size="lg" onClick={this._register}>Register</Button>
                </Form>
                <Footer/>
            </Fragment>
        )
    }
}
