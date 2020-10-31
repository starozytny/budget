import '../../css/pages/homepage.scss';
import React, {Component} from 'react';
import ReactDOM from 'react-dom';
import {Newsletter} from './components/homepage/Newsletter';

import AOS from 'aos/dist/aos'

<<<<<<< HEAD
function experience(){
    let y = new Date();
    let el = document.getElementById('r-compteur');
    if(el){
        ReactDOM.render(
            <Compteur max={y.getFullYear() - parseInt(document.querySelector('#r-compteur').dataset.count)}  timer="25"/>,
            
        );
    }
}
=======
AOS.init();

let el = document.getElementById('newsletter');
if(el){
    ReactDOM.render(<Newsletter />, el)
}

// import Compteur from '../components/composants/Compteur';
//
// experience();
//
// function experience(){
//     let y = new Date();
//     ReactDOM.render(
//         <Compteur max={y.getFullYear() - parseInt(document.querySelector('#r-compteur').dataset.count)}  timer="25"/>,
//         document.getElementById('r-compteur')
//     );
// }
>>>>>>> cd9cc68d3011dbd230c2df03ca788b4b7eaa85e9
