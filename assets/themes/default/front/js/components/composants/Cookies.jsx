import React, {Component} from 'react';
import Analytics          from '../functions/analytics';
import Cookies            from 'js-cookie/src/js.cookie';
import Swal               from 'sweetalert2'

// Nom cookie pour Google analytics
const consentGlobal = 'hasConsentGlobalLocal';
const consentAnalytics = 'hasConsentLocal';

/**
 * Supprimer un cookie
 * @param {string} name 
 */
function delete_cookie(name) {
    document.cookie = name + '=;expires=Thu, 01 Jan 1970 00:00:01 GMT;';
}

/**
 * Logique cookie en fonction du click
 * @param {string} type type de cookie
 * @param {int} action btn -> 1 pour accepter : 2 pour refuser
 */
function cookiesClick(type, action){
    switch (type){
        case 'analytics':
            (action === 1) ? Analytics.startAnalytics(consentAnalytics) : Analytics.stopAnalytics(consentAnalytics)
            break;
        case 'interne':
            console.log(type);
            break;
    }

    // consent all accepte passe to false if action = refuser
    if(action === 0){
        Cookies.set(consentGlobal, false, { expires: 395 });
    }
}

function cookiesAcceptAll(){
    Analytics.startAnalytics(consentAnalytics);
    Cookies.set(consentGlobal, true, { expires: 395 });
}

function cookiesRefuseAll(){
    Analytics.stopAnalytics(consentAnalytics);
    Cookies.set(consentGlobal, false, { expires: 395 });
}

/**
 * Renvoie true false or undefined pour le cookie concernĂ©
 * @param {string} type type de cookie
 */
function cookiesActive(type){
    let consent;
    switch (type){
        case 'analytics':
            consent = consentAnalytics;
            break;
        case 'interne':
            consent = 'interne';
            break;
    }

    return Cookies.getJSON(consent);
}

/**
 * Composant pour les boutons accepter et refuser
 */
export class ActionCookies extends Component {
    constructor(props) {
        super(props);

        this.state = {
            accepter: false,
            refuser: false
        }

        this.handleClickAccept = this.handleClickAccept.bind(this);
        this.handleClickRefuse = this.handleClickRefuse.bind(this);
    }

    //Set active si le cookie existe + true ou existe + false
    componentDidMount () {
        let response = cookiesActive(this.props.type);
        if(response){
            this.setState({ accepter: true, refuser: false })
        }else if(response === false){
            this.setState({ accepter: false, refuser: true })
        }
    }

    handleClickAccept (e){
        this.setState({accepter: true, refuser: false})
        cookiesClick(this.props.type, 1)
        Swal.fire(
            'Choix validĂ© !',
            'Vous avez acceptĂ© ces cookies',
            'success'
            )
    }

    handleClickRefuse (e){
        this.setState({accepter: false, refuser: true})
        cookiesClick(this.props.type, 0)
        Swal.fire(
            'Choix validĂ©!',
            'Vous avez refusĂ© ces cookies',
            'success'
            )
    }

    render () {
        const {type} = this.props
        const {accepter, refuser} = this.state;
        let classAccepter = accepter ? "btn-cookies active" : "btn-cookies";
        let classRefuser = refuser ? "btn-cookies active" : "btn-cookies";

        return (
            <div className="param-cookies-actions">
                <button className={classAccepter} type={type} onClick={this.handleClickAccept}>Accepter</button>
                <button className={classRefuser} type={type} onClick={this.handleClickRefuse}>Refuser</button>
            </div>
        );
    }
}

/**
 * Composant pour la bulle de demande d'acceptation des cookies en gĂ©nĂ©rale ou renvoie vers la page
 * gestion des cookies
 */
export class BulleCookies extends Component {
    constructor(props) {
        super(props);

        this.state = {
            hideBanner : false
        }

        this.handleClickAccept = this.handleClickAccept.bind(this);
        this.handleClickParametre = this.handleClickParametre.bind(this);
    }

    //Set active si le cookie existe + true ou existe + false
    componentDidMount () {
        let hide = false;
        // 1. On rĂ©cupĂ¨re l'Ă©ventuel cookie indiquant le choix passĂ© de l'utilisateur
        const consentCookie = Cookies.getJSON(consentGlobal);
        // 2. On rĂ©cupĂ¨re la valeur "doNotTrack" du navigateur
        const doNotTrack = navigator.doNotTrack || navigator.msDoNotTrack;    
        
        // 3. Si le cookie existe et qu'il vaut explicitement "false" ou que le "doNotTrack" est dĂ©fini Ă  "OUI"
        //    l'utilisateur s'oppose Ă  l'utilisation des cookies. On exĂ©cute une fonction spĂ©cifique pour ce cas.
        if (doNotTrack === 'yes' || doNotTrack === '1') {
            cookiesRefuseAll();
            hide = true;
        }

        // 4. Si le cookie existe et qu'il vaut explicitement "true", on dĂ©marre juste Google Analytics
        // 5. Si le cookie n'existe pas ou que le "doNotTrack" est dĂ©fini Ă  "NON", on crĂ©e le cookie "hasConsent" avec pour
        //    valeur "true" pour une durĂ©e de 13 mois (la durĂ©e maximum autorisĂ©e) puis on dĂ©marre Google Analytics
        if (doNotTrack === 'no' || doNotTrack === '0' || consentCookie === true) {
            cookiesAcceptAll();
            hide = true;
        }

        // 6. Si dans la page de gestion on affiche pas le bandeau
        if(this.props.urlGestion === window.location.pathname || consentCookie === false){
            hide = true;
        }

        if(hide){
            this.setState({hideBanner: true});
            return;
        }

        // 7. Si le cookie n'existe pas et que le "doNotTrack" n'est pas dĂ©fini, alors on affiche le bandeau et on crĂ©e les listeners
        this.setState({hideBanner: false});
    }

    handleClickAccept (e){
        cookiesAcceptAll();
        this.setState({hideBanner: true});
    }

    handleClickParametre (e){
        this.setState({hideBanner: true});
    }

    render () {
        const {urlPolitique, urlGestion} = this.props;
        const {hideBanner} = this.state;
        let className = hideBanner ? 'param-cookies' : 'param-cookies active';

        return (
            <div className={className}>
                <p>
                    En poursuivant votre navigation sur ce site,  vous nous autorisez Ă  dĂ©poser des cookies Ă  des 
                    fins de mesure d'audience et rĂ©aliser des statistiques de visites. <br/>
                    Un cookie sera dĂ©posĂ© afin de mĂ©moriser votre choix. <br/>
                    <a href={urlPolitique}>En savoir plus sur notre politique de confidentialitĂ©</a>
                </p>
                <div className="param-cookies-actions">
                    <button className='btn-cookies' onClick={this.handleClickAccept}>Accepter</button>
                    <a className='btn-cookies' onClick={this.handleClickParametre} href={urlGestion}>ParamĂ©trer les cookies</a>
                </div>
            </div>
        );
    }
}