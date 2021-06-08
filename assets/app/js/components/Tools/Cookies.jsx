import React, { Component } from "react";

import Routing from '@publicFolder/bundles/fosjsrouting/js/router.min';

import { Aside } from "@dashboardComponents/Tools/Aside";

function getCookie(cname) {
    let name = cname + "=";
    let decodedCookie = decodeURIComponent(document.cookie);
    let ca = decodedCookie.split(';');
    for(let i = 0; i <ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) === ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) === 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

function setCookie(cname, cvalue, exdays, cdomain="") {
    let d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    let expires = "expires="+ d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + cdomain + ";path=/";
}

export class CookiesGlobalResponse extends Component {
    constructor(props) {
        super();

        this.state = {
            response: null
        }

        this.handleResponse = this.handleResponse.bind(this);
    }

    componentDidMount = () => {
        const { consent } = this.props;

        let cookie = getCookie(consent);
        if(cookie === "true"){
            this.setState({ response: 1 })
        }else if(cookie === "false"){
            this.setState({ response: 0 })
            let iframe = document.querySelector('.matomo-iframe-display');
            console.log(iframe)
            iframe.classList.add('remove')
        }
    }

    handleResponse = (type) => {
        const { consent, onDisplay } = this.props;

        if(type === 0) { // refusé
            setCookie(consent, false, 30);
        }else{ //accepté
            setCookie(consent, true, 30);
        }

        if(onDisplay){
            onDisplay();
        }
        this.setState({ response: type })
    }

    render () {
        const { fixed, onOpen } = this.props;
        const { response } = this.state;

        return <div className="cookies-choices">
            {fixed && <div onClick={onOpen}>Paramétrer</div>}
            <div onClick={() => this.handleResponse(0)} className={response === 0 ? "active" : ""}>Tout refuser</div>
            <div onClick={() => this.handleResponse(1)} className={response === 1 ? "active" : ""}>Tout accepter</div>
        </div>
    }
}

export class Cookies extends Component {
    constructor(props) {
        super();

        this.state = {
            showCookie: true,
        }

        this.aside = React.createRef();

        this.handleOpen = this.handleOpen.bind(this);
    }

    componentDidMount = () => {
        const { consent } = this.props;

        let cookie = getCookie(consent);
        if(cookie !== ""){
            this.setState({ showCookie: false })
        }
    }

    handleOpen = () => {
        const { consent } = this.props;

        this.aside.current.handleOpen();
        setCookie(consent, "settings", 30);
    }

    handleDisplay = () => {
        this.setState({ showCookie: false })
    }

    render () {
        const { consent } = this.props;
        const { showCookie } = this.state;

        let settings = <div className="aside-cookies-choices">
            <div className="item">
                <span className="title">Cookies nécessaire</span>
                <p>
                    Les cookies nécessaires contribuent à rendre notre site internet utilisable.
                    Par exemple, pour la navigation entre les pages, l'accès aux zones sécurisées,
                    la mémorisation de votre choix d'acceptation des cookies etc.. <br/>
                    Notre site internet ne peut pas fonctionner sans ces cookies.
                </p>
            </div>
            <div className="item">
                <span className="title">Cookies statistiques</span>
                <p>
                    Les cookies statistiques via <b>Matomo</b> nous aident à mesurer l'audience et
                    comprendre comment les visiteurs interagissent avec notre site internet.
                    Ces données ne sont pas identifiable grâce à l'anonymisation de l'adresse IP.
                    De plus, la conservation des données est limitée à 13 mois maximum.
                    <br/>
                    Ces données ne sont pas partagées à un tiers.
                </p>
                <iframe className="matomo-iframe"
                    src="https://matomo.demodirecte.fr/index.php?module=CoreAdminHome&action=optOut&language=fr&backgroundColor=&fontColor=&fontSize=&fontFamily=Poppins"
                />
            </div>
        </div>

        return <>
            {showCookie &&<>
                <div className="cookies">
                    <div className="cookies-title">
                        <div className="biscuit">
                            <img src={'/build/app/images/biscuit.svg'} alt="Cookie illustration"/>
                        </div>
                        <div className="title">Nos cookies</div>
                        <div className="content">
                            Notre site internet utilise des cookies pour vous offrir la meilleure expérience utilisateur.
                            Plus d'informations dans notre <a href={Routing.generate("app_politique")}>politique de confidentialité</a>
                        </div>
                    </div>
                    <CookiesGlobalResponse fixed={true} consent={consent} onOpen={this.handleOpen} onDisplay={this.handleDisplay}/>
                </div>
            </>}
            <Aside ref={this.aside} content={settings}>Paramétrer nos cookies</Aside>
        </>
    }
}