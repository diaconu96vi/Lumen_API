import React, {Component, Fragment} from 'react';
import axios from 'axios';
import {Link, Redirect} from "react-router-dom";

export default class Users extends Component {
    state = {
        users: []
    };

    async componentDidMount() {
        const AuthStr = 'Bearer '.concat(sessionStorage.getItem('token'));
        let users = await axios.get("http://api.tasks.local/v1/admin/users", { headers: { Authorization: AuthStr } });
            /*.then(response => {
                // If request is good...
                console.log(response.data);
            })
            .catch((error) => {
                console.log('error ' + error);
            })*/



        this.setState({users: users.data.data});

    }

    _logout = () => {
        sessionStorage.removeItem('token');

        this.props.history.push('/');
    };

    _table = () => {
        const {users} = this.state;



        {users.map((user, key) => {
            return [
                <tr key={key}>
                    <td>
                        {user.name}
                    </td>
                    <td>
                        {user.email}
                    </td>
                </tr>
            ];
        })}

    };

    render() {
        if (!sessionStorage.getItem('token')) {
            return <Redirect to={'/login'}/>
        }

        return (
            <Fragment>
                <tbody>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                    </tr>
                    {this._table()}
                </tbody>

                <p>Return <Link to={'/'}>Home</Link>.</p>
                <button onClick={this._logout}>Logout</button>
            </Fragment>
        )
    }
}
