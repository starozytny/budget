import React, {Component} from 'react';
import {Input} from '../../../../../react/composants/Fields';

export class Donnee extends Component {
    constructor (props) {
        super ()

        this.state = {
            name: {value: '', error: ''},
            price: {value: '', error: ''}
        }

        this.handleChange = this.handleChange.bind(this)
    }

    handleChange = (e) => {
        this.setState({ [e.currentTarget.name]: {value: e.currentTarget.value} })
    }

    render () {
        const {donnees, title} = this.props
        const {name, price} = this.state

        let items = <div className="objet"><div className="name">Aucune donnée</div></div>

        if(donnees.length != 0){
            items = donnees.map(elem => {
                return <div className="objet">
                    <div className="name">{elem.name}</div>
                    <div className="price">{elem.price} €</div>
                </div>
            })
        }

        return <div className="card-1 card-budget-regular">
            <div className="card-1-header">
                <div className="title">{title}</div>
            </div>
            <div className="card-1-body">
                {items}
            </div>
            <div className="card-1-footer">
                <div className="items">
                    <div className="item">
                        <Input valeur={name} identifiant="name" placeholder="Nom" onChange={this.handleChange} />
                    </div>
                    <div className="item">
                        <Input valeur={price} identifiant="price" placeholder="prix €" onChange={this.handleChange} />
                    </div>
                    <div className="item">
                        <div className="btn-icon"><span className="icon-plus"></span></div>
                    </div>
                </div>
            </div>
        </div>
    }
}