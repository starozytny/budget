import '../../css/pages/security.scss';
import React        from 'react';
import ReactDOM     from 'react-dom';

import {ButtonLost} from './components/security/FormLost';
import {FormReinit} from './components/security/FormReinit';
import {Register}   from './components/security/Register';

formulaireLost();
formulaireReinit();

function formulaireLost(){
    let form = document.querySelector('#lost');

    if(form !== null){
        ReactDOM.render(
            <ButtonLost />,
            form
        );
    }
}

function formulaireReinit(){
    let form = document.querySelector('#form-reinit');

    if(form !== null){
        ReactDOM.render(
            <FormReinit url={form.dataset.url} />,
            form
        );
    }
}

let el = document.querySelector('#register')
if(el != null){
    ReactDOM.render(
        <Register />, el
    )
}