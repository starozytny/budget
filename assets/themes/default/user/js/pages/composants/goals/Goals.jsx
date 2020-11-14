import React, {Component} from 'react';

import toastr             from 'toastr';
import axios              from 'axios';
import Swal               from 'sweetalert2';

import {Page}             from '@reactFolder/composants/page/Page';
import {Aside}            from '@reactFolder/composants/page/Aside';
import Routing            from '@publicFolder/bundles/fosjsrouting/js/router.min.js';
import Loader             from '@reactFolder/functions/loader';
import ActionsArray       from '@reactFolder/functions/actions_array';

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

function getPourcentage(pourcentage){
    let p = 0
    if(pourcentage > 0 && pourcentage <= 35){
        p = 25
    }else if(pourcentage > 35 && pourcentage <= 50){
        p = 50
    }else if(pourcentage > 50 && pourcentage <= 75){
        p = 75
    }else if(pourcentage > 75 && pourcentage < 100){
        p = 85
    }else{
        p = 100
    }
    return p
}

export class Goals extends Component {
    constructor (props){
        super ()

        this.state = {
            goals: JSON.parse(props.goals)
        }

        this.asideGoal = React.createRef()
        this.goal = React.createRef()

        this.handleAdd = this.handleAdd.bind(this)
        this.handleCloseAside = this.handleCloseAside.bind(this)
        this.handleUpdateGoal = this.handleUpdateGoal.bind(this)
        this.handleDelete = this.handleDelete.bind(this)
        this.handleEdit = this.handleEdit.bind(this)
    }

    handleAdd = () => {
        this.asideGoal.current.handleUpdate("Créer un objectif")
        this.goal.current.handleUpdateState("add", null, '', '')
    }

    handleEdit = (id, name, total) => {
        this.asideGoal.current.handleUpdate("Modifier un " + name)
        this.goal.current.handleUpdateState("edit", id, name, total)
    }

    handleUpdateGoal = (goal) => {
        const {goals} = this.state

        this.setState({ goals: ActionsArray.addOrUpdateInArray(goals, goal) })
    }

    handleCloseAside = () => {
        this.asideGoal.current.handleClose()
    }

    handleDelete = (id) => {
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
                axios({ method: 'post', url: Routing.generate('user_goals_delete', {'id': id}) }).then(function (response) {
                    let data = response.data; let code = data.code; Loader.loader(false)

                    if(code === 1){
                        let goal = self.state.goals.filter(v => v.id == id)
                        self.setState({
                            goals: ActionsArray.deleteInArray(self.state.goals, goal[0])
                        })
                        toastr.info('Suppression réussie.')
                    }else{
                        toastr.error(data.message)
                    }
                });
            }
          })
    }

    render () {
        const {goals} = this.state

        let now = new Date();
        let items = goals.map(elem => {

            let totNow = 0, prevYear = 0, prevMonth = 0, finalYear = 0, finalMonth = 0;
            elem.economies.forEach(eco => {
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

            let total = elem.total
            let nPourcentage = Math.round((totNow/total)*100)
            let nStylePourcentage = getPourcentage(nPourcentage)
            let fPourcentage = Math.round((elem.fill/total)*100)
            let fStylePourcentage = getPourcentage(fPourcentage)
            
            let fPourcentageColor;
            if(fPourcentage == 0){
                fPourcentageColor = "grey"
            }else if(fPourcentage == 100){
                fPourcentageColor = "green"
            }else if(fPourcentage > 100){
                fPourcentageColor = "red"
            }else{
                fPourcentageColor = "primary"
            }

            let nPourcentageColor;
            if(nPourcentage == 0){
                nPourcentageColor = "grey"
            }else if(nPourcentage == 100){
                nPourcentageColor = "green"
            }else if(nPourcentage > 100){
                nPourcentageColor = "red"
            }else{
                nPourcentageColor = "primary"
            }


            return <div className="card-1 card-goal" key={elem.id}>
                {/* <div class="card-1-drawing">
                    <div class="image">
                        <img src="{{ asset(path_images ~ "drawing.jpg") }}" alt="illustration">
                    </div>
                </div> */}
                <div className="card-1-header">
                    <div className="title">{elem.name}</div>
                </div>
                <div className="card-1-body">
                    <div className="progress-real">
                        <div className={"c100 p" + nStylePourcentage + " " + nPourcentageColor}>
                            <span>{nPourcentage}%</span>
                            <div className="slice">
                                <div className="bar"></div>
                                <div className="fill"></div>
                            </div>
                        </div>
                        <div>Total atteint au mois de {getMonthString(now.getMonth()).toLowerCase()}</div>
                        <div>{setCurrency(totNow)} / {setCurrency(elem.total)}</div>
                    </div>

                    {elem.fill >= elem.total ? <div className="progress-final">
                        <div className={"c100 p" + fStylePourcentage + " " + fPourcentageColor}>
                            <span>{fPourcentage}%</span>
                            <div className="slice">
                                <div className="bar"></div>
                                <div className="fill"></div>
                            </div>
                        </div>
                        <div>Total atteint en {getMonthString(finalMonth-1).toLowerCase()} {finalYear}</div>
                        <div>{setCurrency(elem.fill)} / {setCurrency(elem.total)}</div>
                    </div> : null}
                                    
                </div>
                <div className="card-1-footer">
                    <div className="items">
                        <div className="item">
                            <div className="btn-icon" onClick={() => this.handleDelete(elem.id)}><span className="icon-trash"></span><span className="tooltip">Supprimer</span></div>
                        </div>
                        <div className="item">
                            <div className="btn-icon" onClick={() => this.handleEdit(elem.id, elem.name, elem.total)}><span className="icon-pencil"></span><span className="tooltip">Modifier</span></div>
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

        
        let asideContent = <Goal ref={this.goal} onUpdateGoal={this.handleUpdateGoal} onCloseAside={this.handleCloseAside} />

        return <>
            <Page content={content} 
                  haveAdd="true" onAdd={this.handleAdd} />
            <Aside content={asideContent} ref={this.asideGoal} />
        </>
    }
}