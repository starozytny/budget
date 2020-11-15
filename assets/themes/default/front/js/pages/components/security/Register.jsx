import React, {Component} from 'react';

import toastr             from 'toastr';
import axios              from 'axios/dist/axios';

import Routing            from '@publicFolder/bundles/fosjsrouting/js/router.min.js';
import Loader             from '@reactFolder/functions/loader';
import {Input}            from '@reactFolder/composants/Fields';
import Validateur         from '@reactFolder/functions/validateur';
import {Alert}            from '@reactFolder/composants/Alert';

export class Register extends Component {
    constructor (props){
        super()

        this.state = {
            error: '',
            username: {value: '', error: ''},
            email: {value: '', error: ''},
            password: {value: '', error: ''},
            passwordConfirme: {value: '', error: ''},
            solde: {value: '', error: ''}
        }

        this.handleChange = this.handleChange.bind(this)
        this.handleSubmit = this.handleSubmit.bind(this)
    }

    handleChange = (e) => { 
        let name = e.currentTarget.name
        let value = e.currentTarget.value
        if(name == "password" || name == "passwordConfirme"){
            this.setState({ [name]: {value: value, error: ''} })
        }
        this.setState({ [name]: {value: value} }) 
    }

    handleSubmit = (e) => {
        e.preventDefault()

        const {username, email, password, passwordConfirme, solde} = this.state

        let validate = Validateur.validateur([
            {type: "text", id: 'username', value: username.value},
            {type: "email", id: 'email', value: email.value},
            {type: "password", id: 'password', value: password.value},
            {type: "text", id: 'solde', value: solde.value}
        ]);

        if(password.value != passwordConfirme.value){
            validate.code = false;
            validate.errors = {...validate.errors, ...{password: {value: password.value, error: 'Les mots de passe ne sont pas identiques.'}}};
        }

        if(!validate.code){
            this.setState(validate.errors);
        }else{
            Loader.loader(true)
            let self = this
            axios({ method: 'post', url: Routing.generate('app_register'), data: self.state }).then(function (response) {
                let data = response.data; let code = data.code; 

                if(code === 1){
                    self.setState({ error: '', success: data.message })
                    setTimeout(() => {
                        location.href = data.url;
                    }, 1500);
                }else{
                    Loader.loader(false);
                    self.setState({ error: data.message, success: ''})
                }
            });
        }
    }

    render () {
        const {username, email, password, passwordConfirme, solde, error} = this.state

        return <form onSubmit={this.handleSubmit}>
            {error != '' ? <Alert type="danger" message={error} active="true" /> : null}
            <div className="line">
                <Input identifiant="username" valeur={username} onChange={this.handleChange} >Nom utilisateur</Input>
            </div>
            <div className="line">
                <Input identifiant="email" valeur={email} onChange={this.handleChange} >Adresse e-mail</Input>
            </div>
            <div className="line line-2">
                <Input type="password" placeholder="****" identifiant="password" valeur={password} onChange={this.handleChange} >Mot de passe</Input>
                <Input type="password" placeholder="****" identifiant="passwordConfirme" valeur={passwordConfirme} onChange={this.handleChange} >Confirmer</Input>
            </div>
            <div className="line">
                <Input type="number" identifiant="solde" placeholder="€" valeur={solde} onChange={this.handleChange} >Solde banque</Input>
            </div>
            <div className="line">
                <button type="submit" className="btn btn-primary">S'enregistrer</button>
            </div>
        </form>
    }
}