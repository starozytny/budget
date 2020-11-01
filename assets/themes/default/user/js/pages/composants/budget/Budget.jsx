import React, {Component} from 'react';
import toastr from 'toastr';
import axios from 'axios';
import Calendrier from '../../../../../react/functions/calendrier';
import {Page} from '../../../../../react/composants/page/Page';
import {Donnee} from './Donnee';
import ActionsArray from '../../../../../react/functions/actions_array';;
import Routing from '../../../../../../../../public/bundles/fosjsrouting/js/router.min.js';
import Loader from '../../../../../react/functions/loader';

export class Budget extends Component {
    constructor (props){
        super ()

        this.state = {
            budget: JSON.parse(props.budget),
            regularSpends: props.regularSpends ? JSON.parse(props.regularSpends) : null
        }

        this.handleUpdate = this.handleUpdate.bind(this)
        this.handleMonth = this.handleMonth.bind(this)
    }

    handleMonth = (id) => {
        Loader.loaderWithoutAjax(true)

        let self = this
        axios({ method: 'post', url: Routing.generate('user_dashboard_month', {'id': id}) }).then(function (response) {
            let data = response.data; let code = data.code; Loader.loader(false)

            if(code === 1){
                self.setState({budget: JSON.parse(data.budget), regularSpends: JSON.parse(data.regularSpends)})
            }else{
                toastr.error(data.message)
            }
        });
    }

    handleUpdate = (type, donnee) => {
        const {regularSpends} = this.state

        let tab; let name;
        switch(type) {
            default:
                name = "regularSpends"
                tab = regularSpends
                break;
        }
            
        this.setState({[name]: ActionsArray.addInArray(tab, donnee)})
    }

    render () {
        const {budget, regularSpends} = this.state

        //Get months
        let months = Calendrier.getMonthsFr().map((elem, index) => {
            return <div key={index} className={"item" + (index+1 == budget.month ? ' active' : '')} onClick={e => {this.handleMonth(index+1)}}>{elem}</div>
        })
        
        console.log(regularSpends)

        //Calcul Total
        let totalRegularSpends = 0;
        if(regularSpends != null){
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
                <Donnee id={budget.id} onUpdateData={this.handleUpdate} add={false} type="regularSpend" donnees={regularSpends} title="Dépenses régulières" />
            </div>
        </div>

        return <>
            <Page infos={infos} content={content} />
        </>
    }
}