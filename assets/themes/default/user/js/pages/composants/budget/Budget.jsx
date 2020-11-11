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

function setCurrency(price){
    return new Intl.NumberFormat("de-DE", {style: "currency", currency: "EUR"}).format(price);
}

export class Budget extends Component {
    constructor (props){
        super ()

        this.state = {
            budgets: JSON.parse(props.budgets),
            budget: JSON.parse(props.budget),
        }

        this.handleUpdateBudget = this.handleUpdateBudget.bind(this)
        this.handleMonth = this.handleMonth.bind(this)
    }

    handleMonth = (id) => {
        let budget = this.state.budgets.filter(v => v.id == id)
        this.setState({ budget: budget[0] })
    }

    handleUpdateBudget = (type, bu) => {
        const {budgets, budget} = this.state

        this.setState({ budgets: ActionsArray.addOrUpdateInArray(budgets, bu), budget: ActionsArray.addOrUpdateInArray(budget, bu)[0] })
    }

    render () {
        const {budgets, budget} = this.state

        //Get months
        let previousToSpend = 0, previousToSpendImmuable = 0;
        let months = [];
        budgets.forEach(elem => {
            let active = '';
            if(elem.month == budget.month){
                active = ' active';
                previousToSpendImmuable = previousToSpend
            }

            months.push(<div key={elem.id} className={"item" + active} onClick={e => {this.handleMonth(elem.id)}}>
                <div>{elem.monthString}</div>
                <div><span className="currency">{setCurrency(elem.toSpend)}</span></div>
            </div>)
            previousToSpend = elem.toSpend
            
        })

        //main
        let infos = <p>Planning pour l'année {budget.year}.</p>

        let content = <div>
            <div className="budget-months">{months}</div>
            <div className="budget-general">
                <div className="card-1 card-budget-toSpend">
                    <div className="card-1-header">
                        <div className="title currency">{setCurrency(budget.toSpend)}</div>
                    </div>
                    <div className="card-1-body">
                        <p>
                            Reste à dépenser pour {budget.monthString}
                        </p>
                    </div>
                </div>
            </div>
            <div className="budget-regular">
                <Donnee id={budget.id} onUpdateBudget={this.handleUpdateBudget} add={false} type="regularSpend" donnees={budget.regularSpends} title="Dépenses régulières" />
            </div>
        </div>

        return <>
            <Page infos={infos} content={content} />
        </>
    }
}