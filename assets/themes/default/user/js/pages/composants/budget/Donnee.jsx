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

export class Donnee extends Component {
    constructor (props) {
        super ()

        this.state = {
            name: {value: '', error: ''},
            price: {value: '', error: ''}
        }

        this.handleChange = this.handleChange.bind(this)
        this.handleAdd = this.handleAdd.bind(this)
        this.handleDelete = this.handleDelete.bind(this)
    }

    handleChange = (e) => {
        this.setState({ [e.currentTarget.name]: {value: e.currentTarget.value} })
    }

    handleDelete = (type, id) => {
        Swal.fire({
            title: 'Etes-vous sûr ?',
            text: "La suppression est définitive.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Oui, je confirme',
            cancelButtonText: 'Annuler'
          }).then((result) => {
            if (result.value) {
                Loader.loader(true)
                let self = this
                axios({ method: 'post', url: Routing.generate('user_donnees_delete', {'type': type, 'id': id}) }).then(function (response) {
                    let data = response.data; let code = data.code; Loader.loader(false)

                    if(code === 1){
                        self.props.onUpdateBudget(data.budget, data.budgets)
                        toastr.info('Suppression réussie.')
                    }else{
                        toastr.error(data.message)
                    }
                });
            }
          })
    }

    handleAdd = (type, id) => {
        const {name, price} = this.state

        let validate = Validateur.validateur([
            {type: "text", id: 'name', value: name.value},
            {type: "text", id: 'price', value: price.value}
        ]);

        if(!validate.code){
            this.setState(validate.errors);
        }else{
            Loader.loaderWithoutAjax(true)

            let self = this
            axios({ method: 'post', url: Routing.generate('user_donnees_add', {'type': type, 'id': id}), data: self.state }).then(function (response) {
                let data = response.data; let code = data.code; Loader.loader(false)

                if(code === 1){
                    self.props.onUpdateBudget(data.budget, data.budgets)
                    self.setState({ 
                        name: {value: '', error: ''},
                        price: {value: '', error: ''}
                     })
                }else{
                    toastr.error(data.message)
                }
            });
        }
    } 

    render () {
        const {id, type, donnees, title} = this.props
        const {name, price} = this.state

        let items = <div className="objet"><div className="name">Aucune donnée.</div></div>
        let total = 0;

        if(donnees.length != 0){
            items = donnees.map((elem, index) => {
                total += elem.price;
                return <div key={index} className="objet">
                    <div className="name">{elem.name}</div>
                    <div className="price currency">{type == "income" ? "+" : "-"} {setCurrency(elem.price)}</div>
                    <div className="delete" onClick={e => {this.handleDelete(type, elem.id)}}><span className="icon-trash"></span></div>
                </div>
            })
        }

        return <div className="card-1 card-budget">
            <div className="card-1-header">
                <div className="title">{title}</div>
                <div className="subtitle">{setCurrency(total)}</div>
            </div>
            <div className="card-1-body">
                {items}
            </div>
            <div className="card-1-footer">
                <form className="items" onSubmit={e => {e.preventDefault(); this.handleAdd(type, id);}}>
                    <div className="item">
                        <Input valeur={name} identifiant="name" id={"name-" + type} placeholder="Nom" onChange={this.handleChange} />
                    </div>
                    <div className="item">
                        <Input type="number" valeur={price} identifiant="price" id={"price-" + type} placeholder="Prix €" onChange={this.handleChange} />
                    </div>
                    <div className="item">
                        <button type="submit" className="btn-icon"><span className="icon-plus"></span></button>
                    </div>
                </form>
            </div>
        </div>
    }
}