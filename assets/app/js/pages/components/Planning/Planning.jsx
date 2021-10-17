import React, { Component } from "react";

import {Months} from "@dashboardComponents/Tools/Days";

export class Planning extends Component {
    constructor(props) {
        super(props);

        this.state = {
            data: [ {day: 1}, {day: 2}, {day: 3}, {day: 4}, {day: 5} ],
            dayActive: 1
        }

        this.handleSelectDay = this.handleSelectDay.bind(this);
    }

    handleSelectDay = (dayActive, atLeastOne) => {
        if(atLeastOne) {
            this.setState({ dayActive })
        }
    }

    render () {
        const { data, dayActive } = this.state;

        return <>
            <Months data={data} dayActive={dayActive} onSelectDay={this.handleSelectDay}/>
        </>
    }
}