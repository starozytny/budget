import React, { Component } from "react";

import { Months } from "@dashboardComponents/Tools/Days";

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
        const { data, monthActive } = this.state;

        console.log(data)

        return <>
            <Months data={data} monthActive={monthActive} onSelectMonth={this.handleSelectMonth}/>
        </>
    }
}