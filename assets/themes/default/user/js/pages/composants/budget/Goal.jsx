import React, {Component} from 'react';

import toastr             from 'toastr';
import axios              from 'axios';
import Swal               from 'sweetalert2';

import {Input}            from '@reactFolder/composants/Fields';
import Routing            from '@publicFolder/bundles/fosjsrouting/js/router.min.js';
import Loader             from '@reactFolder/functions/loader';
import Validateur         from '@reactFolder/functions/validateur';

function setCurrency(price){
    return new Intl.NumberFormat("de-DE", {style: "currency", currency: "EUR"}).format(price);
}

export class Goal extends Component {
    constructor (props) {
        super ()

        this.state = {
            type: "add",
            id: null,
            name: {value: '', error: ''},
            total: {value: '', error: ''}
        }

        this.handleChange = this.handleChange.bind(this)
        this.handleUpdateState = this.handleUpdateState.bind(this)
    }

    handleUpdateState = (type, id, name, total) => {
        this.setState({
            type: type,
            id: id,
            name: {value: name, error: ''},
            total: {value: total, error: ''}
        })
    }

    handleChange = (e) => {
        this.setState({ [e.currentTarget.name]: {value: e.currentTarget.value} })
    }

    handleSubmit = (e) => {
        e.preventDefault();
        
        const {type, id, name, total} = this.state

        let validate = Validateur.validateur([
            {type: "text", id: 'name', value: name.value},
            {type: "text", id: 'total', value: total.value}
        ]);

        if(!validate.code){
            this.setState(validate.errors);
        }else{
            Loader.loader(true)

            let url = type == "add" ? Routing.generate('user_goals_add') : Routing.generate('user_goals_edit', {'id': id})

            let self = this
            axios({ method: 'post', url: url, data: self.state }).then(function (response) {
                let data = response.data; let code = data.code; Loader.loader(false)

                if(code === 1){
                    self.props.onUpdateGoal(data.goal)
                    self.props.onCloseAside()
                }else{
                    self.setState({ name: {value: name.value, error: data.message} })
                }
            });
        }
    }

    render () {
        const {type, name, total} = this.state

        return <form onSubmit={this.handleSubmit}>
            <div className="line line-2">
                <Input identifiant="name" valeur={name} onChange={this.handleChange}>Nom de l'objectif</Input>
                <Input type="custom-number" identifiant="total" valeur={total} onChange={this.handleChange}>Total</Input>
            </div>
            <div className="form-button">
                <button type="submit" className="btn btn-primary"><span>{type == "add" ? "Ajouter" : "Modifier"}</span></button>
            </div>
        </form>
    }
}