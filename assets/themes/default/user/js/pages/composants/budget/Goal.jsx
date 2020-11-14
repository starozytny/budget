import React, {Component} from 'react';

import toastr             from 'toastr';
import axios              from 'axios';
import Swal               from 'sweetalert2';

import {Input}    from '@reactFolder/composants/Fields';
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
            name: {value: '', error: ''},
            total: {value: '', error: ''}
        }

        this.handleChange = this.handleChange.bind(this)
    }

    handleChange = (e) => {
        this.setState({ [e.currentTarget.name]: {value: e.currentTarget.value} })
    }

    handleSubmit = (e) => {
        e.preventDefault();
        
        const {name, total} = this.state

        let validate = Validateur.validateur([
            {type: "text", id: 'name', value: name.value},
            {type: "text", id: 'total', value: total.value}
        ]);

        if(!validate.code){
            this.setState(validate.errors);
        }else{
            Loader.loader(true)

            let self = this
            axios({ method: 'post', url:  Routing.generate('user_goals_add'), data: self.state }).then(function (response) {
                let data = response.data; let code = data.code; Loader.loader(false)

                if(code === 1){
                    
                    self.props.onUpdateGoal(data.goal)
                }else{
                    self.setState({error: data.message})
                }
            });
        }
    }

    render () {
        const {name, total} = this.state

        return <form onSubmit={this.handleSubmit}>
            <div className="line line-2">
                <Input identifiant="name" valeur={name} onChange={this.handleChange}>Nom de l'objectif</Input>
                <Input type="number" identifiant="total" valeur={total} onChange={this.handleChange}>Total</Input>
            </div>
            <div className="form-button">
                <button type="submit" className="btn btn-primary"><span>Ajouter</span></button>
            </div>
        </form>
    }
}