import React, {Component} from 'react';
import Calendrier from '../../../../../react/functions/calendrier';
import {Page} from '../../../../../react/composants/page/Page';

export class Budget extends Component {
    constructor (props){
        super ()

        this.state = {
            budget: JSON.parse(props.budget),
        }
    }
    render () {
        const {budget} = this.state

        let content = <div className="liste">
            <div class="card card-1 card-budget-toSpend">
                <div class="card-budget-toSpend-header">
                    <div class="title">{budget.spend} €</div>
                </div>
                <div class="card-budget-toSpend-body">
                    <p>
                        Reste à dépenser pour {budget.monthString}
                    </p>
                </div>
            </div>
        </div>

        return <>
            <Page content={content} />
        </>
    }
}