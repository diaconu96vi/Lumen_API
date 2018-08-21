import React, {Component, Fragment} from 'react';
import axios from 'axios';
import Header from '../Misc/Header';
import Footer from '../Misc/Footer';

import { Button, Form, FormGroup, Label, Input, Col } from 'reactstrap';

export default class ChangePassword extends Component {
    state = {
        code: '',
        email: '',
        password: ''
    };

    _onChange = (e) => {
        const {name, value} = e.target;

        this.setState({
            [name]: value
        });
    };

    _changePassword = async () => {
        const {code, email, password} = this.state;

        await axios.post(process.env.REACT_APP_API_URL + 'change-password', {
            code, email, password
        });


        this.props.history.push('/login');
    };

    render() {
        const {code, email, password} = this.state;

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
                        <Label for="code" sm={2}>Code</Label>
                        <Col sm={4}>
                            <Input type={'code'} name={'code'} value={code} onChange={this._onChange}/>
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
                    <Button color="info" size="lg" onClick={this._changePassword}>Change password</Button>
                </Form>
                <Footer/>
            </Fragment>
        )
    }
}
