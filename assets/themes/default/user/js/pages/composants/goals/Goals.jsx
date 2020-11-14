import React, {Component} from 'react';

import toastr             from 'toastr';
import axios              from 'axios';
import Swal               from 'sweetalert2';

import {Page}             from '@reactFolder/composants/page/Page';
import {Aside}             from '@reactFolder/composants/page/Aside';
import {Input}            from '@reactFolder/composants/Fields';
import Routing            from '@publicFolder/bundles/fosjsrouting/js/router.min.js';
import Loader             from '@reactFolder/functions/loader';
import Validateur         from '@reactFolder/functions/validateur';

import {Goal}           from '../budget/Goal';

function setCurrency(price){
    return new Intl.NumberFormat("de-DE", {style: "currency", currency: "EUR"}).format(price);
}

function getMonthString(expr){
    switch (expr) {
        case 0: return "Janvier"
        case 1: return "Février"
        case 2: return "Mars"
        case 3: return "Avril"
        case 4: return "Mai"
        case 5: return "Juin"
        case 6: return "Juillet"
        case 7: return "Août"
        case 8: return "Septembre"
        case 9: return "Octobre"
        case 10: return "Novembre"
        default: return "Décembre"
      }
}

export class Goals extends Component {
    constructor (props){
        super ()

        this.state = {
            goals: JSON.parse(props.goals)
        }

        this.asideGoal = React.createRef()

        this.handleAdd = this.handleAdd.bind(this)
        this.handleCloseAside = this.handleCloseAside.bind(this)
        this.handleUpdateGoal = this.handleUpdateGoal.bind(this)
    }

    handleAdd = () => {
        this.asideGoal.current.handleUpdate("Créer un objectif")
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

    render () {
        const {goals} = this.state

        let now = new Date();
        let items = goals.map(elem => {

            let totNow = 0, prevYear = 0, prevMonth = 0, finalYear = 0, finalMonth = 0;
            elem.economy.forEach(eco => {
                let y = eco.budget.year
                let m = eco.budget.month
                if( (y < now.getFullYear()) || (y == now.getFullYear() && m <= now.getMonth() + 1) ){
                    totNow += eco.price
                }

                if((prevYear < y) || (prevYear == y && prevMonth < m) || (prevYear > y)){
                    finalYear = y; finalMonth = m;
                }

                prevYear = y; prevMonth = m;
            })

            return <div className="card-1" key={elem.id}>
                {/* <div class="card-1-drawing">
                    <div class="image">
                        <img src="{{ asset(path_images ~ "drawing.jpg") }}" alt="illustration">
                    </div>
                </div> */}
                <div className="card-1-header">
                    <div className="title">{elem.name}</div>
                </div>
                <div className="card-1-body">
                    <div className="progress">
                        <div>Total atteint au mois de {getMonthString(now.getMonth()).toLowerCase()}</div>
                        <div>{setCurrency(totNow)} / {setCurrency(elem.total)}</div>
                    </div>

                    <div className="progress">
                        <div>Total atteint en {getMonthString(finalMonth-1).toLowerCase()} {finalYear}</div>
                        <div>{setCurrency(elem.fill)} / {setCurrency(elem.total)}</div>
                    </div>                    
                </div>
                <div className="card-1-footer">
                    <div className="items">
                        <div className="item">
                            
                        </div>
                    </div>
                </div>
            </div>
        })

        let content = <div className="liste">
            <div className="card-container">
                <div className="cards-items">
                    {items}
                </div>
            </div>
        </div>

        
        let asideContent = <Goal onUpdateGoal={this.handleUpdateGoal} onCloseAside={this.handleCloseAside} />

        return <>
            <Page content={content} 
                  haveAdd="true" onAdd={this.handleAdd} />
            <Aside content={asideContent} ref={this.asideGoal} />
        </>
    }
}