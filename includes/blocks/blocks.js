import {__} from '@wordpress/i18n';
import {TextControl} from '@wordpress/components';

/**
 * Get language text controls.
 */
export function getLanguageControls(values = {}, setAttributes, attributeKey = 'labels') {
    if (typeof oesLanguageArray === 'undefined') oesLanguageArray = {};

    const textControls = [];
    for (const [langCode, langInfo] of Object.entries(oesLanguageArray)) {
        textControls.push(
            <TextControl
                key={langCode}
                label={langInfo.label}
                value={values[langCode] ?? ''}
                help={__('Add label for this language', 'oes') + ' (' + langCode + ')'}
                onChange={(val) => {
                    const newValues = {...values, [langCode]: val};
                    setAttributes({[attributeKey]: newValues});
                }}
            />
        );
    }

    return textControls;
}

/**
 * Get display value from array.
 */
export function getDisplayValueFromArray(valueArray, defaultString) {
    if (valueArray == null) return defaultString;

    const displayValue = [];
    let defaultValue = '';

    for (const key in valueArray) {
        if (key === 'default') defaultValue = `[${valueArray[key]}]`;
        else displayValue.push(valueArray[key]);
    }

    if (defaultValue.length > 0) displayValue.push(defaultValue);

    return displayValue.length > 0 ? displayValue.join(' / ') : defaultString;
}
