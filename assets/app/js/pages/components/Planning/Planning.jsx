import React, { Component } from "react";

import { Months }       from "@dashboardComponents/Tools/Days";
import { ButtonIcon }   from "@dashboardComponents/Tools/Button";
import { Alert }        from "@dashboardComponents/Tools/Alert";

import Sanitize from "@dashboardComponents/functions/sanitaze";

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
            content = <>
                <div className={"card current " + (elem.end >= 0)}>
                    <div className="card-header">
                        <div className="name">{Sanitize.toFormatCurrency(elem.end)}</div>
                        <div className="sub">
                            <div>Reste à dépenser pour {Sanitize.getMonthStringLong(elem.month)}</div>
                            <div>Compte au début du mois : {Sanitize.toFormatCurrency(elem.start)}</div>
                        </div>
                    </div>
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