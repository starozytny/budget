import React, {Component} from 'react';

import Routing            from '@publicFolder/bundles/fosjsrouting/js/router.min.js';
import {Input}            from '@reactFolder/composants/Fields';
import {Formulaire}       from '@reactFolder/composants/Formulaire';
import Validateur         from '@reactFolder/functions/validateur';
import AjaxSend           from '@reactFolder/functions/ajax_classique';

export class Register extends Component {
    constructor (props){
        super()

        this.state = {
            username: {value: '', error: ''},
            email: {value: '', error: ''},
            password: {value: '', error: ''},
            passwordConfirme: {value: '', error: ''},
            solde: {value: '', error: ''}
        }

        this.handleChange = this.handleChange.bind(this)
    }

    handleChange = (e) => { this.setState({ [e.currentTarget.name]: {value: e.currentTarget.value} }) }

    render () {
        const {username, email, password, passwordConfirme, solde} = this.state

        return <form>
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
                <Input identifiant="solde" placeholder="€" valeur={solde} onChange={this.handleChange} >Solde banque</Input>
            </div>
            <div className="line">
                <button type="submit" className="btn btn-primary">S'enregistrer</button>
            </div>
        </form>
    }
}