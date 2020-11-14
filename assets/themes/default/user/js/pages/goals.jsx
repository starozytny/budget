import '../../css/pages/goals.scss';

import React              from 'react';
import ReactDOM           from 'react-dom';
import {Goals}            from './composants/goals/Goals';

let el = document.getElementById("goals");
if(el){
    ReactDOM.render(<Goals goals={el.dataset.goals} />, el)
}