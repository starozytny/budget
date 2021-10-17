import React, { Component } from "react";

import { Months }       from "@dashboardComponents/Tools/Days";
import {Button, ButtonIcon} from "@dashboardComponents/Tools/Button";
import { Alert }        from "@dashboardComponents/Tools/Alert";

import Sanitize from "@dashboardComponents/functions/sanitaze";
import {icon} from "leaflet/dist/leaflet-src.esm";
import {Input, Select} from "@dashboardComponents/Tools/Fields";
import Validator from "@dashboardComponents/functions/validateur";

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
                    <Card classCard="expenses" data={elem.expenses}>Dépenses occasionnelles</Card>
                </div>

                <div className="planning-line">
                    <Card classCard="expenses" data={elem.expenses}>Dépenses occasionnelles</Card>
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

class Card extends Component {
    constructor(props) {
        super(props);

        this.state = {
            name: "",
            icon: "",
            price: "",
            errors: []
        }

        this.handleChange = this.handleChange.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
    }

    handleChange = (e) => { this.setState({ [e.currentTarget.name]: e.currentTarget.value }) }

    handleSubmit = (e) => {
        e.preventDefault();

        const { name, icon, price } = this.state;

        this.setState({ errors: [] })

        let validate = Validator.validateur([
            {type: "text", id: 'name', value: name},
            {type: "text", id: 'icon', value: icon},
            {type: "text", id: 'price', value: price}
        ])

        if(!validate.code){
            this.setState({ errors: validate.errors });
        }else{
            this.setState({ errors: []});
        }
    }

    render () {
        const { classCard = null, iconCard = "bookmark", children, data } = this.props;
        const { errors, name, icon, price } = this.state;

        let total = 0;

        let icons = [
            { value: 'bookmark', label: 'Bookmark', identifiant: 'bookmark' },
        ]

        return <div className={"card" + (classCard ? " " + classCard : "")}>
            <div className="card-header">
                <div className="icon">
                    <span className={"icon-" + iconCard} />
                </div>
                <div>
                    <div className="name">{children}</div>
                    <div className="sub">{Sanitize.toFormatCurrency(parseFloat(total))}</div>
                </div>
            </div>
            <div className="card-body">
                {data.length !== 0 ? "ok" : "Aucune donnée enregistrée."}
            </div>
            <div className="card-footer">
                <div className="line line-3">
                    <Input identifiant="name" valeur={name} errors={errors} onChange={this.handleChange} placeholder="Intitulé"/>
                    <Input identifiant="price" valeur={price} errors={errors} onChange={this.handleChange} placeholder="Prix €" type="number"/>
                    <Select items={icons} identifiant="icon" valeur={icon} errors={errors} onChange={this.handleChange} placeholder="Icône" />
                </div>
                <div className="form-button">
                    <Button isSubmit={true} icon="plus" type="default" outline={true} onClick={this.handleSubmit}>Ajouter</Button>
                </div>
            </div>
        </div>
    }
}