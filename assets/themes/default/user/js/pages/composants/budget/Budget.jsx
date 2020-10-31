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

        let content = <div>
            <div className="budget-general">
                <div class="card-1 card-budget-toSpend">
                    <div class="card-1-header">
                        <div class="title">{budget.spend} €</div>
                    </div>
                    <div class="card-1-body">
                        <p>
                            Reste à dépenser pour {budget.monthString}
                        </p>
                    </div>
                </div>
            </div>
            <div className="budget-regular">
            <div class="card-1 card-budget-regular">
                    <div class="card-1-header">
                        <div class="title">Dépense régulière</div>
                    </div>
                    <div class="card-1-body">
                        <p>
                            XXXX €
                        </p>
                    </div>
                    <div class="card-1-footer">
                        <div className="items">
                            <div className="item">
                                <input type="text" name="" id=""/>
                            </div>
                            <div className="item">
                                <input type="text" name="" id=""/>
                            </div>
                            <div className="item">
                                <div className="btn-icon"><span className="icon-plus"></span></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        return <>
            <Page content={content} />
        </>
    }
}