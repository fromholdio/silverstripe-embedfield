!function(){"use strict";var e={28:function(e){e.exports=Tip},38:function(e,t,a){var r,n=(r=a(121))&&r.__esModule?r:{default:r};window.document.addEventListener("DOMContentLoaded",(()=>{(0,n.default)()}))},121:function(e,t,a){Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0;var r=s(a(207)),n=s(a(777));function s(e){return e&&e.__esModule?e:{default:e}}t.default=()=>{r.default.component.registerMany({EmbedField:n.default})}},125:function(e,t,a){var r=i(a(669)),n=a(207),s=i(a(594)),l=i(a(518));function i(e){return e&&e.__esModule?e:{default:e}}function o(){return o=Object.assign?Object.assign.bind():function(e){for(var t=1;t<arguments.length;t++){var a=arguments[t];for(var r in a)({}).hasOwnProperty.call(a,r)&&(e[r]=a[r])}return e},o.apply(null,arguments)}r.default.entwine("ss",(e=>{e(".js-injector-boot .form__field-holder > div.embed").entwine({onmatch(){const e=(0,n.loadComponent)("EmbedField"),t=this.data("state")||{};t.value=t.value||"",t.onChange=function(e,a){this.onAutofill(t.name,a.value),t.value=a.value},l.default.render(s.default.createElement(e,o({},t,{onAutofill:(e,t)=>{const a=document.querySelector(`input[name="${e}"]`);a&&(a.value=t)}})),this[0])},onunmatch(){l.default.unmountComponentAtNode(this[0])}})}))},207:function(e){e.exports=Injector},328:function(e,t,a){Object.defineProperty(t,"__esModule",{value:!0}),t.default=function(e,t){let a=arguments.length>2&&void 0!==arguments[2]?arguments[2]:{};if(t&&void 0!==t.react)return n.default.createElement(e,a,t.react);if(t&&void 0!==t.html){if(null!==t.html){const r={__html:t.html};return n.default.createElement(e,s({},a,{dangerouslySetInnerHTML:r}))}return null}let r=null;r=t&&void 0!==t.text?t.text:t;if(r&&"object"==typeof r)throw new Error(`Unsupported string value ${JSON.stringify(r)}`);if(null!=r)return n.default.createElement(e,a,r);return null},t.mapHighlight=function(e,t,a){let r=0,s=e;const l=[],i=t.toLocaleLowerCase();for(;-1!==r;)if(r=s.toLocaleLowerCase().indexOf(i),-1!==r){const e=r+t.length,i=s.substring(0,r),o=s.substring(r,e),u=s.substring(e);i.length&&l.push(i),l.push(a?n.default.createElement(a,{key:l.length/2},o):o),s=u}return l.push(s),l};var r,n=(r=a(594))&&r.__esModule?r:{default:r};function s(){return s=Object.assign?Object.assign.bind():function(e){for(var t=1;t<arguments.length;t++){var a=arguments[t];for(var r in a)({}).hasOwnProperty.call(a,r)&&(e[r]=a[r])}return e},s.apply(null,arguments)}},367:function(e,t,a){Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0;var r=p(a(594)),n=a(556),s=u(a(328)),l=u(a(923)),i=u(a(935)),o=p(a(28));function u(e){return e&&e.__esModule?e:{default:e}}function d(e){if("function"!=typeof WeakMap)return null;var t=new WeakMap,a=new WeakMap;return(d=function(e){return e?a:t})(e)}function p(e,t){if(!t&&e&&e.__esModule)return e;if(null===e||"object"!=typeof e&&"function"!=typeof e)return{default:e};var a=d(t);if(a&&a.has(e))return a.get(e);var r={__proto__:null},n=Object.defineProperty&&Object.getOwnPropertyDescriptor;for(var s in e)if("default"!==s&&{}.hasOwnProperty.call(e,s)){var l=n?Object.getOwnPropertyDescriptor(e,s):null;l&&(l.get||l.set)?Object.defineProperty(r,s,l):r[s]=e[s]}return r.default=e,a&&a.set(e,r),r}t.default=function(e){class t extends r.Component{getMessage(){let e=null;this.props.message&&this.props.message.value&&(e=this.props.message);const t=this.props.meta;return t&&t.error&&t.touched&&(!e||t.dirty)&&(e=t.error),e}getHolderProps(){return{className:(0,l.default)({field:!0,[this.props.extraClass]:!0,readonly:this.props.readOnly}),id:this.props.holderId}}renderMessage(){const e=this.getMessage();if(!e)return null;const t=(0,l.default)(["form__field-message",`form__field-message--${e.type}`]),a=(0,s.default)("div",e.value);return r.default.createElement("div",{className:t},a)}renderLeftTitle(){const e=this.props.leftTitle?this.props.leftTitle:this.props.title;return!e||this.props.hideLabels?null:(0,s.default)(n.Label,e,{className:"form__field-label",for:this.props.id})}renderRightTitle(){return!this.props.rightTitle||this.props.hideLabels?null:(0,s.default)(n.Label,this.props.rightTitle,{className:"form__field-label",for:this.props.id})}renderField(){const t=Boolean(this.getMessage()),a={...this.props,extraClass:(0,l.default)(this.props.extraClass,{"is-invalid":t})},s=r.default.createElement(e,a),i=this.props.data&&this.props.data.prefix?this.props.data.prefix:"",o=this.props.data&&this.props.data.suffix?this.props.data.suffix:"";return i||o?r.default.createElement(n.InputGroup,null,i&&r.default.createElement(n.InputGroupAddon,{addonType:"prepend"},i),s,o&&r.default.createElement(n.InputGroupAddon,{addonType:"append"},o)):s}renderTitleTip(){return this.props.id&&this.props.titleTip&&this.props.titleTip.content?r.default.createElement(o.default,{id:`FieldHolder-${this.props.id}-titleTip`,content:this.props.titleTip.content,fieldTitle:this.props.title,type:o.TIP_TYPES.TITLE,icon:"menu-help"}):null}renderDescription(){return null===this.props.description?null:(0,s.default)("div",this.props.description,{className:"form__field-description"})}render(){return this.props.noHolder?this.renderField():r.default.createElement(n.FormGroup,this.getHolderProps(),this.renderLeftTitle(),this.renderTitleTip(),r.default.createElement("div",{className:"form__field-holder"},this.renderField(),this.renderMessage(),this.renderDescription()),this.renderRightTitle())}}return t.propTypes={leftTitle:i.default.any,rightTitle:i.default.any,title:i.default.any,extraClass:i.default.string,holderId:i.default.string,id:i.default.string,name:i.default.string,description:i.default.any,hideLabels:i.default.bool,message:i.default.shape({extraClass:i.default.string,value:i.default.any,type:i.default.string}),data:i.default.oneOfType([i.default.array,i.default.shape({prefix:i.default.string,suffix:i.default.string})]),titleTip:i.default.shape(o.tipShape)},t.defaultProps={className:"",extraClass:"",leftTitle:null,rightTitle:null,title:"",description:null,hideLabels:!1,noHolder:!1,message:null,data:{}},t}},518:function(e){e.exports=ReactDom},556:function(e){e.exports=Reactstrap},594:function(e){e.exports=React},669:function(e){e.exports=jQuery},777:function(e,t,a){Object.defineProperty(t,"__esModule",{value:!0}),t.default=t.Component=void 0;var r=function(e,t){if(!t&&e&&e.__esModule)return e;if(null===e||"object"!=typeof e&&"function"!=typeof e)return{default:e};var a=o(t);if(a&&a.has(e))return a.get(e);var r={__proto__:null},n=Object.defineProperty&&Object.getOwnPropertyDescriptor;for(var s in e)if("default"!==s&&{}.hasOwnProperty.call(e,s)){var l=n?Object.getOwnPropertyDescriptor(e,s):null;l&&(l.get||l.set)?Object.defineProperty(r,s,l):r[s]=e[s]}return r.default=e,a&&a.set(e,r),r}(a(594)),n=a(556),s=i(a(935)),l=i(a(367));function i(e){return e&&e.__esModule?e:{default:e}}function o(e){if("function"!=typeof WeakMap)return null;var t=new WeakMap,a=new WeakMap;return(o=function(e){return e?a:t})(e)}class u extends r.Component{constructor(e){super(e),this.state={embedData:e.embedData||{},embedMessage:e.embedMessage||"",loading:!1,inputValue:e.value||""},this.handleChange=this.handleChange.bind(this),this.handleButtonClick=this.handleButtonClick.bind(this)}getInputProps(){const e={className:`${this.props.className} ${this.props.extraClass}`,id:this.props.id,name:this.props.name,disabled:this.props.disabled,readOnly:this.props.readOnly,placeholder:this.props.placeholder,autoFocus:this.props.autoFocus,maxLength:this.props.data&&this.props.data.maxlength,type:this.props.type?this.props.type:null,onBlur:this.props.onBlur,onFocus:this.props.onFocus,value:this.state.inputValue};return this.props.attributes&&!Array.isArray(this.props.attributes)&&Object.assign(e,this.props.attributes),this.props.readOnly||Object.assign(e,{onChange:this.handleChange}),e}handleChange(e){const t=e.target.value;if(this.setState({inputValue:t}),"function"==typeof this.props.onChange){if(!e.target)return;this.props.onChange(e,{id:this.props.id,value:t})}}handleButtonClick(){this.setState({loading:!0});const e=this.props.previewURL,t=document.querySelector('input[name="SecurityID"]'),a=t?t.value:"";fetch(e,{method:"POST",headers:{"Content-Type":"application/json","X-SecurityID":a},body:JSON.stringify({source_url:this.state.inputValue})}).then((e=>e.json())).then((e=>{if("success"===e.status)this.setState({embedData:e.data,embedMessage:"",loading:!1});else{const t=e.message&&""!==e.message.trim()?e.message:"That didn't work, please refresh and try again.";this.setState({embedData:e.data,embedMessage:t,loading:!1})}})).catch((e=>{console.error("Error:",e),this.setState({embedData:data.data,embedMessage:"Something went wrong, please refresh and try again.",loading:!1})}))}render(){return r.default.createElement("div",{className:"embedfield"},r.default.createElement("div",{className:"embedfield-preview",dangerouslySetInnerHTML:{__html:this.state.embedData.previewHTML||""}}),r.default.createElement("p",{className:"embedfield-message",style:{display:this.state.embedMessage?"block":"none"}},this.state.embedMessage),r.default.createElement(n.InputGroup,{className:"test"},r.default.createElement(n.Input,this.getInputProps()),r.default.createElement(n.InputGroupAddon,{addonType:"append"},r.default.createElement(n.Button,{type:"button",color:"primary",onClick:this.handleButtonClick,disabled:this.state.loading},this.state.loading?"Loading...":"Preview"))))}}t.Component=u,u.propTypes={extraClass:s.default.string,id:s.default.string,name:s.default.string.isRequired,onChange:s.default.func,onBlur:s.default.func,onFocus:s.default.func,value:s.default.oneOfType([s.default.string,s.default.number]),readOnly:s.default.bool,disabled:s.default.bool,placeholder:s.default.string,type:s.default.string,autoFocus:s.default.bool,attributes:s.default.oneOfType([s.default.object,s.default.array]),previewURL:s.default.string,embedData:s.default.oneOfType([s.default.object,s.default.array]),embedMessage:s.default.string},u.defaultProps={value:"",extraClass:"",className:"",type:"text",attributes:{},embedData:{},embedMessage:""};t.default=(0,l.default)(u)},923:function(e){e.exports=classnames},935:function(e){e.exports=PropTypes}},t={};function a(r){var n=t[r];if(void 0!==n)return n.exports;var s=t[r]={exports:{}};return e[r](s,s.exports,a),s.exports}a(125),a(38)}();