import React from 'react';
import { Button, Header, Modal, Icon } from 'semantic-ui-react'
import { connect } from 'react-redux'
import { closeModal } from './redux/frame/actions'
import dataDefinitions from './dataDefinitions'

export default connect(state => {
    return state.frame;
}, dispatch => {
    return {
        closeModal: () => closeModal(dispatch)
    };
})(class extends React.Component {

    render() {
        return (
            <Modal
                open={this.props.modal.isOpen}
                size="tiny"
                closeOnDimmerClick={false}
                closeIcon={this.props.modal.abortable}
                onClose={this.props.modal.abortable ? () => this.props.closeModal() : null}
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
                                {this.props.modal.actionButtonText}
                            </Button>
                        ) : null
                    }
                    {
                        this.props.modal.abortable ? (
                            <Button
                                color="grey"
                                onClick={() => this.props.closeModal()}
                            >
                                Bezár
                            </Button>
                        ) : null
                    }
                </Modal.Actions>
            </Modal>
        );
    }

})
