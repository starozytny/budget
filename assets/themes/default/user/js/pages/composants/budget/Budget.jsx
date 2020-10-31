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
            <div class="card-1">
                <div class="card-1-header">
                    <div class="title">Reste à dépenser pour</div>
                </div>
                <div class="card-1-body">
                    <p>
                        <b>{budget.spend} €</b>
                    </p>
                </div>
            </div>
        </div>

        return <>
            <Page content={content} />
        </>
    }
}