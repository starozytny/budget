import React, {Component} from 'react';
import Calendrier from '../../../../../react/functions/calendrier';
import {Page} from '../../../../../react/composants/page/Page';
import {Donnee} from './Donnee';

export class Budget extends Component {
    constructor (props){
        super ()

        this.state = {
            budget: JSON.parse(props.budget),
            regularSpends: JSON.parse(props.regularSpends)
        }
    }
    render () {
        const {budget, regularSpends} = this.state

        let totalRegularSpends = 0;
        regularSpends.forEach(elem => {
            totalRegularSpends += elem.price
        })

        console.log(totalRegularSpends)

        let total = budget.spend - totalRegularSpends

        let content = <div>
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
                <Donnee id={budget.id} donnees={regularSpends} title="Dépenses régulières" />
                <Donnee id={budget.id} donnees={regularSpends} title="Entrées d'argent" />
            </div>
        </div>

        return <>
            <Page content={content} />
        </>
    }
}