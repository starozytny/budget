import React, {Component} from 'react';

import toastr             from 'toastr';
import axios              from 'axios';
import Swal               from 'sweetalert2';

import {Input, Select}    from '@reactFolder/composants/Fields';
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
            price: {value: '', error: ''},
            goal: {value: '', error: ''},
        }

        this.handleChange = this.handleChange.bind(this)
        this.handleAdd = this.handleAdd.bind(this)
        this.handleDelete = this.handleDelete.bind(this)
        this.handleSelectGoal = this.handleSelectGoal.bind(this)
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
                        self.props.onUpdateBudgets(data.budget, data.budgets, data.goals)
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
                    self.props.onUpdateBudgets(data.budget, data.budgets, data.goals)
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

    handleSelectGoal = (goal) => {
        this.setState({ goal: {value: goal.id, error: ''} })
    }

    render () {
        const {id, budget, type, donnees, title, goals, onOpenAside} = this.props
        const {name, price, goal} = this.state

        let items = <div className="objet"><div className="name">Aucune donnée.</div></div>
        let total = 0;

        // for select goals
        let goalsItems = [{'value': "none", 'libelle': 'Aucun objectif'}];
        let goalsUntilThisMonth = []
        if(goals){
            goals.forEach(elem => {

                let tot = 0;

                elem.economies.forEach(eco => {
                    if( (eco.budget.year < budget.year) || (eco.budget.year == budget.year && eco.budget.month <= budget.month) ){
                        tot += eco.price
                    }
                })

                goalsUntilThisMonth.push({'id': elem.id, 'tot': tot})
                goalsItems.push( {'value': elem.id, 'libelle': elem.name} )
            })
        }

        if(donnees.length != 0){
            items = donnees.map((elem, index) => {
                total += elem.price;
                let pourcentage, goalDiff;

                if(elem.goal){

                    let fill = 0;
                    goalsUntilThisMonth.forEach(goalUntilThisMonth => {
                        if(goalUntilThisMonth.id == elem.goal.id){
                            fill = goalUntilThisMonth.tot
                        }
                    })

                    let total = elem.goal.total
                    pourcentage = Math.round((fill/total)*100)
                    goalDiff = fill - total
                    
                    if(pourcentage > 0 && pourcentage <= 35){
                        pourcentage = 25
                    }else if(pourcentage > 35 && pourcentage <= 50){
                        pourcentage = 50
                    }else if(pourcentage > 50 && pourcentage <= 75){
                        pourcentage = 75
                    }else if(pourcentage > 75 && pourcentage < 100){
                        pourcentage = 85
                    }else{
                        pourcentage = 100
                    }
                }

                return <div key={index} className="objet">
                    <div className="name">
                        <div>{elem.name} {elem.goal ? <span className="goal">- {elem.goal.name} ({setCurrency(elem.goal.total)})</span> : null}</div> 
                        {elem.goal ? <div className={"goal-progress progress-" + pourcentage + (goalDiff > 0 ? ' progress-overkill' : '')}></div> : null}
                        {goalDiff > 0 ? <div className="goal-overkill">{"surplus de " + setCurrency(goalDiff)}</div> : null}
                    </div>
                    <div className="price currency">{type == "income" ? "+" : "-"} {setCurrency(elem.price)}</div>
                    <div className="delete" onClick={e => {this.handleDelete(type, elem.id)}}><span className="icon-trash"></span></div>
                </div>
            })
        }
        
        return <div className="card-1 card-budget">
            <div className="card-1-header">
                <div className={"card-1-header-type " + type}>
                    <span className="icon-bookmark"></span>
                </div>
                <div className="card-1-header-infos">
                    <div className="title">{title}</div>
                    <div className="subtitle">{setCurrency(total)}</div>
                </div>
            </div>
            <div className="card-1-body">
                {items}
            </div>
            <div className="card-1-footer">
                <form className="items" onSubmit={e => {e.preventDefault(); this.handleAdd(type, id);}}>
                    <div className="item item-name">
                        <Input valeur={name} identifiant="name" id={"name-" + type} placeholder="Nom" onChange={this.handleChange} />
                    </div>
                    {goals && goalsItems.length != 0 ? <div className="item item-goal">
                        <Select valeur={goal} identifiant="goal" placeholder="Objectif" onChange={this.handleChange} items={goalsItems}></Select>
                    </div> : null}
                    <div className="item item-price">
                        <Input type="custom-number" valeur={price} identifiant="price" id={"price-" + type} placeholder="Prix €" onChange={this.handleChange} />
                    </div>
                    <div className="item">
                        <button type="submit" className="btn-icon"><span className="icon-plus"></span></button>
                    </div>
                </form>
                {goals ? <div className="add-goal" onClick={onOpenAside}>Ajouter un objectif</div> : null}
            </div>
        </div>
    }
}