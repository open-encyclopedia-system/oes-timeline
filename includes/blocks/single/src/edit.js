import {__} from '@wordpress/i18n';
import {useBlockProps} from '@wordpress/block-editor';
import {CheckboxControl} from '@wordpress/components';
import {getLanguageControls, getDisplayValueFromArray} from '../../blocks';
import '../../../../assets/timeline.css';

export default function Edit({attributes, setAttributes, isSelected}) {

    const {className, detail, labels} = attributes;

    if (isSelected) {
        return (
            <div {...useBlockProps()}>
                <div className="components-placeholder components-placeholder is-large">
                    <div className="components-placeholder__label">{__('Timeline', 'oes')}</div>
                    <div className="oes-block-further-information">{__('The timeline options can be defined ' +
                        'in the OES schema settings.', 'oes')}</div>
                    <CheckboxControl
                        label={__('Display as detail block', 'oes')}
                        checked={detail}
                        onChange={(val) => setAttributes({
                            detail: val
                        })}
                    />
                    <div className="oes-block-subheader">{__('Header', 'oes')}</div>
                    {getLanguageControls(labels, setAttributes)}
                </div>
            </div>
        );
    } else {
        return (
            <div {...useBlockProps()}>
                {detail ?
                    (<details className={className + ' oes-timeline-container wp-block-details'} open>
                        <summary><span className="">{getDisplayValueFromArray(labels, '')}</span>
                        </summary>
                        <div className="oes-timeline-container">
                            <div className="oes-timeline-outer">
                                <div className="oes-timeline-year" id="oes_timeline_year_1880">1880</div>
                                <div className="oes-timeline-event-wrapper">
                                    <div className="oes-timeline-event ">
                                        <div><span className="oes-timeline-event-title"><a
                                            href="http://localhost/oes/events/duden-first-published/">1880</a></span>First
                                            publication of Duden Wörterbuch
                                        </div>
                                    </div>
                                </div>
                                <div className="oes-timeline-year" id="oes_timeline_year_1918">1918</div>
                                <div className="oes-timeline-event-wrapper">
                                    <div className="oes-timeline-event oes-timeline-range">
                                        <div><span className="oes-timeline-event-title"><a
                                            href="http://localhost/oes/events/weimar-republic/">1918 – 1933</a></span>Weimar
                                            Republic
                                        </div>
                                    </div>
                                </div>
                                <div className="oes-timeline-year" id="oes_timeline_year_1951">1951</div>
                                <div className="oes-timeline-event-wrapper">
                                    <div className="oes-timeline-event ">
                                        <div><span className="oes-timeline-event-title"><a
                                            href="http://localhost/oes/events/leipzig-duden/">1951</a></span>First East-German
                                            Duden
                                            edition published
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </details>) :
                    (<div className={className + ' oes-timeline-container'}>
                        <h5 className="oes-content-table-header">{getDisplayValueFromArray(labels, '')}</h5>
                        <div className="oes-timeline-container">
                            <div className="oes-timeline-outer">
                                <div className="oes-timeline-year" id="oes_timeline_year_1880">1880</div>
                                <div className="oes-timeline-event-wrapper">
                                    <div className="oes-timeline-event ">
                                        <div><span className="oes-timeline-event-title"><a
                                            href="http://localhost/oes/events/duden-first-published/">1880</a></span>First
                                            publication of Duden Wörterbuch
                                        </div>
                                    </div>
                                </div>
                                <div className="oes-timeline-year" id="oes_timeline_year_1918">1918</div>
                                <div className="oes-timeline-event-wrapper">
                                    <div className="oes-timeline-event oes-timeline-range">
                                        <div><span className="oes-timeline-event-title"><a
                                            href="http://localhost/oes/events/weimar-republic/">1918 – 1933</a></span>Weimar
                                            Republic
                                        </div>
                                    </div>
                                </div>
                                <div className="oes-timeline-year" id="oes_timeline_year_1951">1951</div>
                                <div className="oes-timeline-event-wrapper">
                                    <div className="oes-timeline-event ">
                                        <div><span className="oes-timeline-event-title"><a
                                            href="http://localhost/oes/events/leipzig-duden/">1951</a></span>First East-German
                                            Duden
                                            edition published
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>)
                }
            </div>
        );
    }
}
