import '../css/default.scss';
import React, {Component} from 'react';
import ReactDOM from 'react-dom';
import {Budget} from './pages/composants/budget/Budget';

let el = document.getElementById("budget");
if(el){
    ReactDOM.render(<Budget budgets={el.dataset.budgets} budget={el.dataset.budget} previousBudget={el.dataset.previousBudget} />, el)
}