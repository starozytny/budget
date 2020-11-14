import React, {Component} from 'react';

import toastr             from 'toastr';
import axios              from 'axios';

import {TextAreaWys}      from '@reactFolder/composants/Fields';
import Routing            from '@publicFolder/bundles/fosjsrouting/js/router.min.js';
import Loader             from '@reactFolder/functions/loader';

export class Comment extends Component {
    constructor (props) {
        super ()

        this.state = {
            comment: {value: '', error: '', html: ''}
        }

        this.trumbowyg = React.createRef();

        this.handleChangeText = this.handleChangeText.bind(this)
    }

    handleUpdateComment = (comment) => {
        this.setState({comment: {value: comment == null ? '' : comment, error: '', html: comment}})
    }

    handleChangeText = (e) => { 
        this.setState({comment: {value: this.state.comment.value == null ? '' : this.state.comment.value, error: '', html: e.currentTarget.innerHTML}}) 
    }

    handleSubmit = (e) => {
        e.preventDefault();

        Loader.loader(true)

        let self = this
        axios({ method: 'post', url:  Routing.generate('user_budgets_add_comment', {'id': self.props.id}), data: self.state }).then(function (response) {
            let data = response.data; let code = data.code; Loader.loader(false)

            if(code === 1){
                self.props.onUpdateBudgets(data.budget, data.budgets)
                self.props.onCloseAside()
            }else{
                toastr.error(data.message)
            }
        });
    }

    render () {
        const {comment} = this.state

        return <form onSubmit={this.handleSubmit}>
            <div className="line">
                <TextAreaWys valeur={comment} identifiant="comment" reference={this.trumbowyg} onChange={this.handleChangeText}>Contenu</TextAreaWys>
            </div>
            <div className="form-button">
                <button type="submit" className="btn btn-primary"><span>Valider le commentaire</span></button>
            </div>
        </form>
    }
}