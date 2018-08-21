import React, {Component} from 'react';
import {Redirect} from 'react-router-dom';
import {
    Navbar,
    NavbarBrand,
    Nav,
    NavItem,
    NavLink} from 'reactstrap';

export default class Header extends Component {
    state = {
        redirect: false
    };

    _logout = () => {
        if(sessionStorage.getItem('token')){
            sessionStorage.removeItem('token');

            this.setState({
                redirect: true
            });
        }

    };

    render() {
        if (this.state.redirect) {
            return <Redirect to={'/login'}/>;
        }

        return (
            <div className={'header'}>
                <Navbar color="dark" dark expand="md">
                    <NavbarBrand>Practica RoWeb 2018</NavbarBrand>
                        <Nav className="ml-auto" navbar>
                            <NavItem>
                                <NavLink href="/">Home</NavLink>
                            </NavItem>
                            <NavItem>
                                <NavLink href="/login">Login</NavLink>
                            </NavItem>
                            <NavItem>
                                <NavLink href="/register">Register</NavLink>
                            </NavItem>
                            <NavItem>
                                <NavLink href="#" onClick={this._logout}>Logout</NavLink>

                            </NavItem>
                        </Nav>
                </Navbar>
            </div>
        );
    }
}