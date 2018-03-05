import React from 'react';
import Top from './Top';
import Side from './Side';
import Major from './Major';
import { Button, Header, Menu, Modal, Icon, Segment } from 'semantic-ui-react'
import { connect } from 'react-redux'
import { closeModal } from '../../redux/frame/actions'
import { fetchProject } from '../../redux/projects/actions'
import dataDefinitions from '../../dataDefinitions'

import 'semantic-ui-css/semantic.min.css';
import './frame.css';

export default connect(state => {
    return state.frame;
}, dispatch => {
    return {
        closeModal: () => closeModal(dispatch)
    };
})(class extends React.Component {

    render() {
        return (
            <div>
                <div className="frame-outer">
                    <div className="frame-top">
                        <Top />
                    </div>
                    <div className="frame-page">
                        <div className="frame-side">
                            <Side minimized={this.props.side.minimized} />
                        </div>
                        <div className="frame-major">
                            <Major />
                        </div>
                    </div>
                </div>
                <Modal
                    open={this.props.modal.isOpen}
                    size="tiny"
                    closeOnDimmerClick={false}
                    closeIcon={true}
                    onClose={() => this.props.closeModal()}
                >
                    <Header
                        icon={<Icon
                            name={dataDefinitions.frame.modal.modalTypeInfo[this.props.modal.modalType].icon}
                            color={dataDefinitions.frame.modal.modalTypeInfo[this.props.modal.modalType].color}
                        />}
                        content={this.props.modal.title}
                    />
                    <Modal.Content>
                        {this.props.modal.content}
                    </Modal.Content>
                    <Modal.Actions>
                        {
                            this.props.modal.action ? (
                                <Button
                                    color={dataDefinitions.frame.modal.modalTypeInfo[this.props.modal.modalType].color}
                                    onClick={() => {
                                        this.props.modal.action();
                                        this.props.closeModal();
                                    }}
                                >
                                    Rajta!
                                </Button>
                            ) : null
                        }
                        <Button
                            color="grey"
                            onClick={() => this.props.closeModal()}
                        >
                            Bezár
                        </Button>
                    </Modal.Actions>
                </Modal>
            </div>
        );
    }

})
