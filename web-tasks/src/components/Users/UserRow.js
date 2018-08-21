import React, {Component} from 'react';
import {Row, Col, Button} from 'reactstrap';
import PropTypes from 'prop-types';
import axios from 'axios';
import {Redirect} from "react-router-dom";

export default class UserRow extends Component {
    state = {
        redirect: false
    };
    static propTypes = {
        user: PropTypes.object.isRequired,
        edit: PropTypes.func.isRequired
    };

    _showRole = role => {
        switch (role) {
            case 1:
                return 'Admin';
            case 2:
                return 'User';
            default:
                return 'Unknown'
        }
    };

    _edit = (user) => {
        const {edit} = this.props;

        edit && edit(user);
    };

    _delete = async(id) => {
        let res = await axios.delete(process.env.REACT_APP_API_URL + `admin/user/${id}`);

        if (res && res.data && res.data.responseType === 'success') {
            this.setState({
                redirect: true
            });
        }

    };

    render() {
        if (this.state.redirect) {
            return <Redirect to={'/users'}/>;
        }

        const {user} = this.props;

        const style = {
            border: "1px solid #c0392b",
            backgroundColor: "#F0F8FF"
        }

        return (
            <Row>
                <Col style={style} xs={1}>{user.id}</Col>
                <Col style={style} xs={3}>{user.name}</Col>
                <Col style={style} xs={3}>{user.email}</Col>
                <Col style={style} xs={2}>{this._showRole(user.role_id)}</Col>
                <Col style={style} xs={3}>
                    <Button color="success" size="lg" onClick={() => this._edit(user)}>Edit</Button>
                    <Button color="danger" size="lg" onClick={() => this._delete(user.id)}>Delete</Button>
                </Col>
            </Row>
        );
    }
}