import React, { Component } from "react";

import { Months } from "@dashboardComponents/Tools/Days";
import { ButtonIcon } from "@dashboardComponents/Tools/Button";

export class Planning extends Component {
    constructor(props) {
        super(props);

        this.state = {
            yearActive: (new Date()).getFullYear(),
            monthActive: (new Date()).getMonth() + 1
        }

        this.handleSelectMonth = this.handleSelectMonth.bind(this);
    }

    componentDidMount = () => {
        const { donnees } = this.props;
        const { yearActive } = this.state;


        let data = [];
        JSON.parse(donnees).forEach(elem => {
            if(parseInt(elem.year) === yearActive){
                data.push(elem);
            }
        })

        this.setState({ data: data })
    }

    handleSelectMonth = (monthActive, atLeastOne) => {
        if(atLeastOne) {
            this.setState({ monthActive })
        }
    }

    render () {
        const { data, yearActive, monthActive } = this.state;

        console.log(data)

        return <>
            <div className="years">
                <ButtonIcon icon="left-arrow">{yearActive - 1}</ButtonIcon>
                <div className="current">{yearActive}</div>
                <ButtonIcon icon="right-arrow">{yearActive + 1}</ButtonIcon>
            </div>
            <Months data={data} monthActive={monthActive} onSelectMonth={this.handleSelectMonth}/>
        </>
    }
}