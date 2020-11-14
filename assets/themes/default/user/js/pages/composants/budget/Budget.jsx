import React, {Component} from 'react';

import toastr             from 'toastr';
import axios              from 'axios';

import Routing            from '@publicFolder/bundles/fosjsrouting/js/router.min.js';
import {Aside}            from '@reactFolder/composants/page/Aside';
import Loader             from '@reactFolder/functions/loader';
import ActionsArray       from '@reactFolder/functions/actions_array';
import {Page}             from '@reactFolder/composants/page/Page';

import {Donnee}           from './Donnee';
import {Goal}           from './Goal';
import {Comment}           from './Comment';

let HtmlToReactParser = require('html-to-react').Parser;

function setCurrency(price){
    return new Intl.NumberFormat("de-DE", {style: "currency", currency: "EUR"}).format(price);
}

export class Budget extends Component {
    constructor (props){
        super ()

        this.state = {
            budgets: JSON.parse(props.budgets),
            budget: JSON.parse(props.budget),
            previousBudget: JSON.parse(props.previousBudget),
            goals: JSON.parse(props.goals),
        }

        this.asideGoal = React.createRef();
        this.donnee = React.createRef();

        this.asideComment = React.createRef();

        this.handleUpdateBudgets = this.handleUpdateBudgets.bind(this)
        this.handleMonth = this.handleMonth.bind(this)
        this.handleChangeYear = this.handleChangeYear.bind(this)

        this.handleOpenAsideComment = this.handleOpenAsideComment.bind(this)
        this.handleOpenAsideGoal = this.handleOpenAsideGoal.bind(this)

        this.handleCloseAside = this.handleCloseAside.bind(this)
        this.handleUpdateGoal = this.handleUpdateGoal.bind(this)
    }

    handleMonth = (id) => {
        let budget = this.state.budgets.filter(v => v.id == id)
        this.setState({ budget: budget[0] })
    }

    handleUpdateBudgets = (bu, bus) => {
        this.setState({ budgets: JSON.parse(bus), budget: JSON.parse(bu) })
    }

    handleUpdateGoal = (goal) => {
        const {goals} = this.state

        this.setState({ goals: ActionsArray.addOrUpdateInArray(goals, goal) })
        this.donnee.current.handleSelectGoal(JSON.parse(goal))
    }

    handleCloseAside = () => {
        this.asideGoal.current.handleClose()
        this.asideComment.current.handleClose()
    }

    handleChangeYear = (direction, y) => {
        Loader.loaderWithoutAjax(true)
        let self = this
        axios({ method: 'post', url: Routing.generate('user_dashboard_year', {'direction': direction, 'year': y}) }).then(function (response) {
            let data = response.data; let code = data.code; Loader.loader(false)

            if(code === 1){
                self.setState({ budgets: JSON.parse(data.budgets), budget: JSON.parse(data.budget) })
            }else{
                toastr.error(data.message)
            }
        });
    }

    handleOpenAsideGoal = () => { this.asideGoal.current.handleUpdate("Créer un objectif") }
    handleOpenAsideComment = (type) => { this.asideComment.current.handleUpdate(type + ' - Quoi de neuf ?') }

    render () {
        const {budgets, budget, previousBudget, goals} = this.state

        //Get months
        let months = [];
        budgets.forEach(elem => {
            months.push(<div key={elem.id} className={"item" + (elem.month == budget.month ? ' active' : '')} onClick={e => {this.handleMonth(elem.id)}}>
                <div>{elem.monthString}</div>
                <div><span className="currency">{setCurrency(elem.toSpend)}</span></div>
            </div>)
        })

        //main
        let infos = <div className="budget-years">
            <p>Planning pour l'année {budget.year}.</p>
            <div className="years">
                <div className="item" onClick={e => this.handleChangeYear('previous', budget.year-1)}><span className="icon-left-arrow"></span></div>
                <div className="item active">{budget.year}</div>
                <div className="item" onClick={e => this.handleChangeYear('next', budget.year+1)}><span className="icon-right-arrow"></span></div>
            </div>
        </div>
        
        let htmlToReactParser = new HtmlToReactParser()

        let content = <div>
            <div className="budget-months">{months}</div>
            <div className="budget-general">
                <div className="budget-general-container">
                    <div className={"card-1 card-budget-toSpend " + (budget.toSpend > 0 ? 'positive' : 'negative')}>
                        <div className="card-1-header">
                            <div className="title">{setCurrency(budget.toSpend)}</div>
                        </div>
                        <div className="card-1-body">
                            <p>
                                Reste à dépenser pour {budget.monthString} <br/>
                                Compte au début du mois {setCurrency(budget.initMonth)}
                            </p>
                        </div>
                    </div>
                    <div className="card-1 card-comment">
                        <div className="card-1-header">
                            <div className="title">Quoi de neuf ?</div>
                             <div className="btn-icon" onClick={() => {this.handleOpenAsideComment(budget.comment ? "Modifier" : "Ajouter")}}>
                                <span className="icon-pencil"></span><span className="tooltip tooltip-bot-right">{budget.comment ? "Modifier" : "Ajouter"}</span>
                            </div>
                        </div>
                        <div className="card-1-body">
                            <p>{budget.comment ? htmlToReactParser.parse(budget.comment) : "Rien ce mois-ci."}</p>
                        </div>
                        <div className="card-1-footer"> <div className="items"> <div className="item"></div> </div> </div>
                    </div>
                </div>
            </div>
            <div className="budget-cards">
                <div className="budget-cards-container">
                    <Donnee id={budget.id} onUpdateBudgets={this.handleUpdateBudgets}
                            type="regularSpend" donnees={budget.regularSpends} title="Dépenses régulières" 
                    />
                    <Donnee id={budget.id} onUpdateBudgets={this.handleUpdateBudgets}
                            type="income" donnees={budget.incomes} title="Gains réguliers" 
                    />
                    <Donnee id={budget.id} onUpdateBudgets={this.handleUpdateBudgets} goals={goals} onOpenAside={this.handleOpenAsideGoal} ref={this.donnee}
                            type="economy" donnees={budget.economies} title="Economies" 
                    />
                </div>
            </div>
            <div className="budget-outgos">
                <Donnee id={budget.id} onUpdateBudgets={this.handleUpdateBudgets} type="outgo" donnees={budget.outgos} title="Dépenses occasionnelles" />
            </div>
        </div>

        let asideContent = <Goal onUpdateGoal={this.handleUpdateGoal} onCloseAside={this.handleCloseAside} />
        let asideComment = <Comment id={budget.id} comment={budget.comment} onUpdateBudgets={this.handleUpdateBudgets} onCloseAside={this.handleCloseAside} />

        return <>
            <Page infos={infos} content={content} />
            <Aside content={asideContent} ref={this.asideGoal} />
            <Aside content={asideComment} ref={this.asideComment} />
        </>
    }
}