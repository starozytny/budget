import React, {Component} from 'react';
import axios              from 'axios/dist/axios';

import Routing            from '@publicFolder/bundles/fosjsrouting/js/router.min.js';
import Loader             from '@reactFolder/functions/loader';
import Validateur         from '@reactFolder/functions/validateur';
import {Input, TextArea}  from '@reactFolder/composants/Fields';
import {Drop}             from '@reactFolder/composants/Drop';

function getBase64(file, self) {
    var reader = new FileReader();
    reader.readAsDataURL(file);
    reader.onload = function () {
        self.setState({logo: {value: reader.result} })
    };
    reader.onerror = function (error) {
      console.log('Error: ', error);
    };
 }

export class Settings extends Component {
    constructor (props){
        super ()

        let emailGlobal = '', emailContact = '', emailRgpd = '', websiteName = '', logo = '', maxYear = '';

        if(props.settings != undefined && props.settings != ''){
            let data = JSON.parse(JSON.parse(props.settings))
            let setting = data[0];
            emailGlobal = setting.emailGlobal
            emailContact = setting.emailContact
            emailRgpd = setting.emailRgpd
            websiteName = setting.websiteName
            maxYear = setting.maxYear
            logo = setting.logo
        }
        
        this.state = {
            emailGlobal: {value: emailGlobal, error: ''},
            emailContact: {value: emailContact, error: ''},
            emailRgpd: {value: emailRgpd, error: ''},
            websiteName: {value: websiteName, error: ''},
            maxYear: {value: maxYear, error: ''},
            logo: {value: logo, error: ''}
        }

        this.handleSubmit = this.handleSubmit.bind(this)
        this.handleChange = this.handleChange.bind(this)
        this.handleGetFile = this.handleGetFile.bind(this)
    }

    handleChange = (e) => {
        this.setState({[ e.currentTarget.name]: {value: e.currentTarget.value}}) 
    }

    handleGetFile = (e) => { 
        getBase64(e.file, this)
    }

    handleSubmit = (e) => {
        e.preventDefault()

        const {emailGlobal, emailContact, emailRgpd, websiteName, logo, maxYear} = this.state

        let validate = Validateur.validateur([
            {type: "email", id: 'emailGlobal', value: emailGlobal.value},
            {type: "email", id: 'emailContact', value: emailContact.value},
            {type: "email", id: 'emailRgpd', value: emailRgpd.value},
            {type: "text", id: 'websiteName', value: websiteName.value},
            {type: "number", id: 'maxYear', value: maxYear.value},
            {type: "text", id: 'logo', value: logo.value}
        ]);

        if(!validate.code){
            this.setState(validate.errors);
        }else{
            Loader.loader(true)

            let self = this
            axios({ method: 'post', url: Routing.generate('super_settings_edit'), data: self.state }).then(function (response) {
                let data = response.data; let code = data.code; 
                if(code === 1){
                    location.reload()
                }else{
                    Loader.loader(false)
                    self.setState({error: data.message})
                }
            });
        }
    }

    render () {
        const {emailGlobal, emailContact, emailRgpd, websiteName, logo, maxYear} = this.state
        const {isDanger} = this.props

        return <>
            <div className={"card-dash " + (isDanger == 1 ? 'card-dash-danger' : '')}>
                <div className="card-body">
                    {isDanger == 1 ? <span className="txt-alpha"><span className="icon-warning"></span>Veuillez configurer les param??tres du sites.</span> : null}
                    
                    <form onSubmit={this.handleSubmit}>
                        <div className="line line-2">
                            <Input type="text" identifiant="websiteName" valeur={websiteName} onChange={this.handleChange}>Nom du site</Input>
                            <Input type="email" identifiant="emailGlobal" valeur={emailGlobal} onChange={this.handleChange}>E-mail exp??diteur global</Input>
                        </div>
                        <div className="line line-2">
                            <Input type="email" identifiant="emailContact" valeur={emailContact} onChange={this.handleChange}>E-mail destinataire contact</Input>
                            <Input type="email" identifiant="emailRgpd" valeur={emailRgpd} onChange={this.handleChange}>E-mail destinataire RGPD</Input>
                        </div>
                        <div className="line">
                            <label>Logo pour les <u>mails</u></label>
                            <div className="form-files">
                                {logo.value === "" ? null : <div className="form-logo"><img src={logo.value} alt="logo actuel du site internet"/></div>}
                                <Drop label="T??l??verser le logo" labelError="Seul les fichiers au format jpeg, jpg et png sont accept??es."
                                    accept={"image/*"} maxFiles={1} onGetFile={this.handleGetFile}/>
                            </div>
                        </div>
                        <div className="line">
                            <Input type="number" identifiant="maxYear" valeur={maxYear} onChange={this.handleChange}>Max d'ann??es</Input>
                        </div>
                        <div className="form-button">
                            <button type="submit" className="btn btn-primary"><span>Valider</span></button>
                        </div>
                    </form>
                </div>
            </div>
        </>
    }
}