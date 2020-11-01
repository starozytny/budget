import React, {Component} from 'react';
import toastr from 'toastr';
import axios from 'axios';
import Calendrier from '../../../../../react/functions/calendrier';
import {Page} from '../../../../../react/composants/page/Page';
import {Donnee} from './Donnee';
import ActionsArray from '../../../../../react/functions/actions_array';;
import Routing from '../../../../../../../../public/bundles/fosjsrouting/js/router.min.js';
import Loader from '../../../../../react/functions/loader';

function getType(type, self){
    let name, tab;
    switch(type) {
        default:
            name = "regularSpends"
            tab = self.state.regularSpends
            break;
    }

    return [name, tab]
}

export class Budget extends Component {
    constructor (props){
        super ()

        let b = JSON.parse(props.budget);

        this.state = {
            budgets: JSON.parse(props.budgets),
            budget: b,
            regularSpends: b.regularSpends
        }

        this.handleUpdateBudget = this.handleUpdateBudget.bind(this)
        this.handleDeleteDonnee = this.handleDeleteDonnee.bind(this)
        this.handleMonth = this.handleMonth.bind(this)
    }

    handleMonth = (id) => {
        let budget = this.state.budgets.filter(v => v.id == id)
        console.log(budget)
        this.setState({ budget: budget[0], regularSpends: budget[0].regularSpends })
    }

    handleUpdateBudget = (type, donnee) => {
        const {budgets, budget} = this.state
        let data = getType(type, this)
        let name = data[0]; let tab = data[1]; 

        let bs = [];
        budgets.forEach(elem => {
            if(elem.id == budget.id){
                elem.regularSpends.push(JSON.parse(donnee))
            }
            bs.push(elem)
        })
        this.setState({ budgets: bs, [name]: ActionsArray.addInArray(tab, donnee) })
    }

    handleDeleteDonnee = (type, id) => {
        let data = getType(type, this)
        let name = data[0]; let tab = data[1];

        let donnee = tab.filter(v => v.id == id)
        this.setState({ [name]: ActionsArray.deleteInArray(tab, donnee[0]) })
    }

    render () {
        const {budgets, budget, regularSpends} = this.state

        //Get months
        let months = budgets.map(elem => {
            return <div key={elem.id} className={"item" + (elem.month == budget.month ? ' active' : '')} onClick={e => {this.handleMonth(elem.id)}}>{elem.monthString}</div>
        })

        //Calcul Total
        let totalRegularSpends = 0;
        if(regularSpends.length != 0){
            regularSpends.forEach(elem => {
                totalRegularSpends += elem.price
            })
        }

        let total = budget.spend - totalRegularSpends

        //main
        let infos = <p>Planning pour l'année {budget.year}.</p>

        let content = <div>
            <div className="budget-months">{months}</div>
            <div className="budget-general">
                <div className="card-1 card-budget-toSpend">
                    <div className="card-1-header">
                        <div className="title">{total} €</div>
                    </div>
                    <div className="card-1-body">
                        <p>
                            Reste à dépenser pour {budget.monthString}
                        </p>
                    </div>
                </div>
            </div>
            <div className="budget-regular">
                <Donnee id={budget.id} onUpdateBudget={this.handleUpdateBudget} onDeleteDonnee={this.handleDeleteDonnee} add={false} type="regularSpend" donnees={regularSpends} title="Dépenses régulières" />
            </div>
        </div>

        return <>
            <Page infos={infos} content={content} />
        </>
    }
}