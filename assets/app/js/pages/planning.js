import '../../css/pages/planning.scss';

const routes = require('@publicFolder/js/fos_js_routes.json');
import Routing from '@publicFolder/bundles/fosjsrouting/js/router.min';

import React from 'react';
import { render } from 'react-dom';
import { Planning } from "./components/Planning/Planning";

Routing.setRoutingData(routes);

let el = document.getElementById("planning");
if(el){
    render(<Planning {...el.dataset} />, el)
}