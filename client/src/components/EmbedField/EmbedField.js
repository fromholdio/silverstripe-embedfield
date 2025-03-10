import React, { Component } from 'react';
import { Input, InputGroup, InputGroupAddon, Button } from 'reactstrap';
import PropTypes from 'prop-types';
import fieldHolder from '../../lib/FieldHolder';

class EmbedField extends Component {
    constructor(props) {
        super(props);
        this.state = {
            // Initialize from props, then manage locally
            embedData: props.embedData || {},
            embedMessage: props.embedMessage || '',
            loading: false,
            inputValue: props.value || '',
        };
        this.handleChange = this.handleChange.bind(this);
        this.handleButtonClick = this.handleButtonClick.bind(this);
    }

    getInputProps() {
        const props = {
            className: `${this.props.className} ${this.props.extraClass}`,
            id: this.props.id,
            name: this.props.name,
            disabled: this.props.disabled,
            readOnly: this.props.readOnly,
            placeholder: this.props.placeholder,
            autoFocus: this.props.autoFocus,
            maxLength: this.props.data && this.props.data.maxlength,
            type: this.props.type ? this.props.type : null,
            onBlur: this.props.onBlur,
            onFocus: this.props.onFocus,
            // Use local state for the input's value.
            value: this.state.inputValue,
        };

        if (this.props.attributes && !Array.isArray(this.props.attributes)) {
            Object.assign(props, this.props.attributes);
        }

        if (!this.props.readOnly) {
            Object.assign(props, {
                onChange: this.handleChange,
            });
        }

        return props;
    }

    handleChange(event) {
        const newValue = event.target.value;
        this.setState({ inputValue: newValue });
        if (typeof this.props.onChange === 'function') {
            if (!event.target) {
                return;
            }
            this.props.onChange(event, { id: this.props.id, value: newValue });
        }
    }

    handleButtonClick() {
        this.setState({ loading: true });
        const endpoint = this.props.previewURL;
        const tokenElement = document.querySelector('input[name="SecurityID"]');
        const securityToken = tokenElement ? tokenElement.value : '';

        fetch(endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-SecurityID': securityToken,
            },
            body: JSON.stringify({ source_url: this.state.inputValue })
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.status === 'success') {
                    // On success, update local state with the new embedData (including previewHTML)
                    // and clear any message.
                    this.setState({
                        embedData: data.data,
                        embedMessage: '',
                        loading: false,
                    });
                } else {
                    // For any other status, update the message accordingly.
                    const msg =
                        data.message && data.message.trim() !== ''
                            ? data.message
                            : "That didn't work, please refresh and try again.";
                    this.setState({
                        embedData: data.data,
                        embedMessage: msg,
                        loading: false,
                    });
                }
            })
            .catch((error) => {
                console.error('Error:', error);
                this.setState({
                    embedData: data.data,
                    embedMessage: "Something went wrong, please refresh and try again.",
                    loading: false,
                });
            });
    }

    render() {
        return (
            <div className="embedfield">
                {/* Insert previewHTML from local state */}
                <div
                    className="embedfield-preview"
                    dangerouslySetInnerHTML={{ __html: (this.state.embedData.previewHTML) || '' }}
                ></div>
                <p
                    className="embedfield-message"
                    style={{ display: this.state.embedMessage ? 'block' : 'none' }}
                >
                    {this.state.embedMessage}
                </p>
                <InputGroup className="test">
                    <Input {...this.getInputProps()} />
                    <InputGroupAddon addonType="append">
                        <Button
                            type="button"
                            color="primary"
                            onClick={this.handleButtonClick}
                            disabled={this.state.loading}
                        >
                            {this.state.loading ? "Loading..." : "Preview"}
                        </Button>
                    </InputGroupAddon>
                </InputGroup>
            </div>
        );
    }
}

EmbedField.propTypes = {
    extraClass: PropTypes.string,
    id: PropTypes.string,
    name: PropTypes.string.isRequired,
    onChange: PropTypes.func,
    onBlur: PropTypes.func,
    onFocus: PropTypes.func,
    value: PropTypes.oneOfType([PropTypes.string, PropTypes.number]),
    readOnly: PropTypes.bool,
    disabled: PropTypes.bool,
    placeholder: PropTypes.string,
    type: PropTypes.string,
    autoFocus: PropTypes.bool,
    attributes: PropTypes.oneOfType([PropTypes.object, PropTypes.array]),
    previewURL: PropTypes.string,
    embedData: PropTypes.oneOfType([PropTypes.object, PropTypes.array]),
    embedMessage: PropTypes.string,
};

EmbedField.defaultProps = {
    value: '',
    extraClass: '',
    className: '',
    type: 'text',
    attributes: {},
    embedData: {},
    embedMessage: '',
};

export { EmbedField as Component };
export default fieldHolder(EmbedField);
