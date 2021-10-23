import React, { Component } from "react";

import axios        from "axios";
import toastr       from "toastr";
import Swal         from "sweetalert2";
import SwalOptions  from "@dashboardComponents/functions/swalOptions";
import Routing      from '@publicFolder/bundles/fosjsrouting/js/router.min.js';

import { Months }               from "@dashboardComponents/Tools/Days";
import { Alert }                from "@dashboardComponents/Tools/Alert";
import { Input, Select }        from "@dashboardComponents/Tools/Fields";
import { Button, ButtonIcon }   from "@dashboardComponents/Tools/Button";

import Sanitize     from "@dashboardComponents/functions/sanitaze";
import Validator    from "@dashboardComponents/functions/validateur";
import Formulaire   from "@dashboardComponents/functions/Formulaire";

function removeItemToPlanning(who, planning, elem) {
    switch (who){
        case "gains":
            planning.gains = planning.gains.filter(el => el.id !== elem.id);
            break;
        case "economies":
            planning.economies = planning.economies.filter(el => el.id !== elem.id);
            break;
        case "incomes":
            planning.incomes = planning.incomes.filter(el => el.id !== elem.id);
            break;
        case "outcomes":
            planning.outcomes = planning.outcomes.filter(el => el.id !== elem.id);
            break;
        default:
            planning.expenses = planning.expenses.filter(el => el.id !== elem.id);
            break;
    }

    return planning;
}

function appendItemToPlanning(who, planning, elem) {
    switch (who){
        case "gains":
            planning.gains.push(elem)
            break;
        case "economies":
            planning.economies.push(elem)
            break;
        case "incomes":
            planning.incomes.push(elem)
            break;
        case "outcomes":
            planning.outcomes.push(elem)
            break;
        default:
            planning.expenses.push(elem);
            break;
    }

    return planning;
}

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
        this.handleUpdateData = this.handleUpdateData.bind(this);
    }

    handleSelectYear = (yearActive) => { this.setState({ yearActive }) }

    handleSelectMonth = (monthActive, atLeastOne) => {
        if(atLeastOne) {
            this.setState({ monthActive })
        }
    }

    handleUpdateData = (context, who, elem=null) => {
        const { data, yearActive, monthActive } = this.state;


        let newData = [];
        switch (context){
            case "delete":
                data.forEach(item => {
                    if(item.year === yearActive && item.month === monthActive){
                        item = removeItemToPlanning(who, item, elem);
                    }

                    newData.push(item);
                })

                break;
            default:
                data.forEach(item => {
                    if(item.id === elem.planning.id){
                        item = appendItemToPlanning(who, item, elem);
                    }

                    newData.push(item);
                })
                break;
        }

        this.setState({ data: newData })
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

            // console.log(elem)

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

                <div className="planning-line planning-line-3">
                    <Card onUpdateData={this.handleUpdateData} planning={elem.id} data={elem.outcomes} classCard="regular" who="outcomes">Dépenses fixes</Card>
                    <Card onUpdateData={this.handleUpdateData} planning={elem.id} data={elem.expenses} classCard="income" who="incomes">Gains fixes</Card>
                    <Card onUpdateData={this.handleUpdateData} planning={elem.id} data={elem.expenses} classCard="economy" who="economies">Economies</Card>
                </div>

                <div className="planning-line">
                    <Card onUpdateData={this.handleUpdateData} planning={elem.id} data={elem.expenses} classCard="expenses" who="expenses" enableSpread={false}>Dépenses occasionnelles</Card>
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
            planning: props.planning,
            name: "",
            icon: "",
            price: "",
            errors: []
        }

        this.handleChange = this.handleChange.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
        this.handleSpread = this.handleSpread.bind(this);
        this.handleDelete = this.handleDelete.bind(this);
    }

    handleChange = (e) => { this.setState({ [e.currentTarget.name]: e.currentTarget.value }) }

    handleSubmit = (e) => {
        e.preventDefault();

        const { who } = this.props;
        const { name, icon, price } = this.state;

        this.setState({ errors: [] })

        let validate = Validator.validateur([
            {type: "text", id: 'name', value: name},
            {type: "text", id: 'price', value: price}
        ])

        if(!validate.code){
            this.setState({ errors: validate.errors });
        }else{
            this.setState({ errors: [] });

            Formulaire.loader(true);
            let self = this;

            axios.request({ method: "POST", url: Routing.generate('api_'+ who +'_create'), data: this.state })
                .then(function (response) {
                    let data = response.data;
                    self.props.onUpdateData('create', who, data);
                    toastr.info("Félicitation ! L'ajout s'est réalisé avec succès !");
                })
                .catch(function (error) {
                    Formulaire.displayErrors(self, error);
                })
                .then(function () {
                    Formulaire.loader(false);
                })
            ;
        }
    }

    handleSpread = (elem) => {
        const { who } = this.props;

        Swal.fire(SwalOptions.options(
            "Diffuser cette donnée sur les prochains mois de cette année ?",
            elem.name + " : " + Sanitize.toFormatCurrency(elem.price)))
            .then((result) => {
                if (result.isConfirmed) {
                    Formulaire.loader(true);
                    let self = this;

                    axios.request({ method: "POST", url: Routing.generate('api_'+ who +'_spread', {id: elem.id}) })
                        .then(function (response) {
                            let data = response.data;
                            console.log(data);
                            toastr.info("Diffusion Réussie !");
                        })
                        .catch(function (error) {
                            console.log(error)
                            console.log(error.response)
                            Formulaire.displayErrors(self, error);
                        })
                        .then(function () {
                            Formulaire.loader(false);
                        })
                    ;
                }
            })
        ;
    }

    handleDelete = (elem) => {
        const { who } = this.props;

        Swal.fire(SwalOptions.options("Supprimer cette donnée ?",""))
            .then((result) => {
                if (result.isConfirmed) {
                    Formulaire.loader(true);
                    let self = this;

                    axios.request({ method: "DELETE", url: Routing.generate('api_'+ who +'_delete', {id: elem.id}) })
                        .then(function (response) {
                            self.props.onUpdateData('delete', who, elem);
                            toastr.info("Suppression réussie !");
                        })
                        .catch(function (error) {
                            Formulaire.displayErrors(self, error);
                        })
                        .then(function () {
                            Formulaire.loader(false);
                        })
                    ;
                }
            })
        ;
    }

    render () {
        const { classCard = null, iconCard = "bookmark", children, data, enableSpread = true } = this.props;
        const { errors, name, icon, price } = this.state;

        let total = 0;
        data.forEach(elem => {
            total += elem.price;
        })

        let icons = [
            { value: 'bookmark', label: 'Bookmark', identifiant: 'bookmark' },
        ]

        let items = [];
        data.forEach(elem => {
            items.push(<div className="item" key={elem.id}>
                <div className="col-1">{elem.name}</div>
                <div className="col-2">-{Sanitize.toFormatCurrency(elem.price)}</div>
                <div className="col-3 actions">
                    <ButtonIcon icon="trash" onClick={() => this.handleDelete(elem)}>Supprimer</ButtonIcon>
                    {enableSpread &&  <ButtonIcon icon="share" onClick={() => this.handleSpread(elem)}>Diffuser</ButtonIcon>}
                </div>
            </div>)
        })

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
                {items.length !== 0 ? items : "Aucune donnée enregistrée."}
            </div>
            <div className="card-footer">
                <div className="line line-3">
                    <Input identifiant="name" valeur={name} errors={errors} onChange={this.handleChange} placeholder="Intitulé"/>
                    <Input identifiant="price" valeur={price} errors={errors} onChange={this.handleChange} placeholder="Prix €" type="number"/>
                    <Select items={icons} identifiant="icon" valeur={icon} errors={errors} onChange={this.handleChange} placeholder="Icône" />
                </div>
                <div className="form-button">
                    <Button isSubmit={true} type="default" outline={true} onClick={this.handleSubmit}>Ajouter</Button>
                </div>
            </div>
        </div>
    }
}