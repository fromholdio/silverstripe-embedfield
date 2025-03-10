import jQuery from "jquery";
import { loadComponent } from "lib/Injector";
import React from "react";
import ReactDOM from "react-dom";

jQuery.entwine("ss", ($) => {
    // We're matching to the field based on class. We added the last class in the field
    $(".js-injector-boot .form__field-holder > div.embed").entwine({
        onmatch() {
            // We're using the injector to create an instance of the react component we can use
            const Component = loadComponent("EmbedField");
            // We've added the schema state to the div in the template above which we'll use as props
            const schemaState = this.data("state") || {};

            // This is our "polyfill" for `onAutoFill`
            const setValue = (fieldName, value) => {
                // We'll find the input by name, we shouldn't ever have the same input
                // with the same name or form state will be messed up
                const input = document.querySelector(`input[name="${fieldName}"]`);

                // If there's no input field then we'll return early
                if (!input) {
                    return;
                }

                // Now we can set the field value
                input.value = value;
            };

            // Ensure value is defined
            schemaState.value = schemaState.value || '';
            // Supply an onChange that uses onAutofill to update the input field in the DOM
            schemaState.onChange = function(e, update) {
                // update.value should be the new value typed in by the user
                // Call the onAutofill provided by the entwine wrapper to update the input field
                this.onAutofill(schemaState.name, update.value);
                // Optionally, also update the schemaState object if you plan on re-rendering manually
                schemaState.value = update.value;
            };

            // We render the component onto the targeted div
            ReactDOM.render(
                <Component {...schemaState} onAutofill={setValue} />,
                this[0]
            );
        },

        // When we change the loaded page we'll remove the component
        onunmatch() {
            ReactDOM.unmountComponentAtNode(this[0]);
        },
    });
});
