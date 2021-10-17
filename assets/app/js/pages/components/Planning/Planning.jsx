import React, { Component } from "react";

import { Months }       from "@dashboardComponents/Tools/Days";
import { ButtonIcon }   from "@dashboardComponents/Tools/Button";
import { Alert }        from "@dashboardComponents/Tools/Alert";

import Sanitize from "@dashboardComponents/functions/sanitaze";
import {icon} from "leaflet/dist/leaflet-src.esm";

export class Planning extends Component {
    constructor(props) {
        super(props);

        this.state = {
            data: JSON.parse(props.donnees),
            yearActive: (new Date()).getFullYear(),
            monthActive: (new Date()).getMonth() + 1
        }

        this.handleSelectYear = this.handleSelectYear.bind(this);
        this.handleSelectMonth = this.handleSelectMonth.bind(this);
    }

    handleSelectYear = (yearActive) => { this.setState({ yearActive }) }

    handleSelectMonth = (monthActive, atLeastOne) => {
        if(atLeastOne) {
            this.setState({ monthActive })
        }
    }

    render () {
        const { data, yearActive, monthActive } = this.state;

        let items = []; let elem = null;
        data.forEach(element => {
            if(parseInt(element.year) === yearActive){
                items.push(element);

                if(parseInt(element.month) === monthActive){
                    elem = element;
                }
            }
        })

        let content = <Alert>Aucune donnée disponible.</Alert>
        if(elem){

            console.log(elem)

            content = <>
                <div className="planning-line">
                    <div className={"card current " + (elem.end >= 0)}>
                        <div className="card-header">
                            <div>
                                <div className="name">{Sanitize.toFormatCurrency(elem.end)}</div>
                                <div className="sub">
                                    <div>Reste à dépenser pour {Sanitize.getMonthStringLong(elem.month)}</div>
                                    <div>Compte au début du mois : {Sanitize.toFormatCurrency(elem.start)}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div className="planning-line">
                    <Card classCard="expenses" price={0} data={elem.expenses}>Dépenses occasionnelles</Card>
                </div>
            </>
        }

        return <>
            <div className="years">
                <ButtonIcon icon="left-arrow" onClick={() => this.handleSelectYear(yearActive - 1)} >{yearActive - 1}</ButtonIcon>
                <div className="current">{yearActive}</div>
                <ButtonIcon icon="right-arrow" onClick={() => this.handleSelectYear(yearActive + 1)} >{yearActive + 1}</ButtonIcon>
            </div>
            <Months data={items} monthActive={monthActive} onSelectMonth={this.handleSelectMonth}/>
            <div className="content">
                {content}
            </div>
        </>
    }
}

function Card ({ classCard = null, iconCard = "bookmark", children, price, data }) {
    return <div className={"card" + (classCard ? " " + classCard : "")}>
        <div className="card-header">
            <div className="icon">
                <span className={"icon-" + iconCard} />
            </div>
            <div>
                <div className="name">{children}</div>
                <div className="sub">{Sanitize.toFormatCurrency(parseFloat(price))}</div>
            </div>
        </div>
        <div className="card-body">
            {data.length !== 0 ? "ok" : "Aucune donnée enregistrée."}
        </div>
        <div className="card-footer">
            
        </div>
    </div>
}