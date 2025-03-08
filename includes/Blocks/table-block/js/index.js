const { registerBlockType } = wp.blocks;
const { createElement, useEffect, useState } = wp.element;
const { useBlockProps, InspectorControls } = wp.blockEditor;
const { PanelBody, ToggleControl, Spinner } = wp.components;

registerBlockType("akash-api-plugin/table-block", {
    edit: function (props) {
        const { attributes, setAttributes } = props;

        if (attributes.previewImage) {
            return createElement("img", {
                src: akashApiPluginData.pluginBlocksFolderUrl + props.attributes.previewImage,
                style: { width: "100%", height: "auto" }
            });
        }

        const [tableData, setTableData] = useState(null);
        const [loading, setLoading] = useState(true);

        useEffect(() => {
            fetch(akashApiPluginData.ajaxUrl, {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: new URLSearchParams({
                    action: akashApiPluginData.action,
                    security: akashApiPluginData.nonce,
                    attributes: JSON.stringify(attributes) // Block attributes
                }),
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.success) {
                        setTableData(data.data.html);
                    } else {
                        setTableData("<p>Error loading data.</p>");
                    }
                    setLoading(false);
                })
                .catch(() => setLoading(false));
        }, [attributes]); // Refresh block when attributes change

        const blockProps = useBlockProps({
            className: "akash-api-plugin-table-block",
        });

        return createElement(
            "div",
            blockProps,
            [
                createElement(
                    InspectorControls,
                    { key: "controls" },
                    createElement(
                        PanelBody,
                        { title: "Table Column Visibility", initialOpen: true },
                        createElement(ToggleControl, {
                            label: "Show Name Column",
                            checked: attributes.showName,
                            onChange: (value) => setAttributes({ showName: value })
                        }),
                        createElement(ToggleControl, {
                            label: "Show Email Column",
                            checked: attributes.showEmail,
                            onChange: (value) => setAttributes({ showEmail: value })
                        }),
                        createElement(ToggleControl, {
                            label: "Show Phone Column",
                            checked: attributes.showPhone,
                            onChange: (value) => setAttributes({ showPhone: value })
                        })
                    )
                ),
                createElement(
                    "div",
                    { className: "table-container" },
                    loading ? createElement(Spinner) : createElement("div", { dangerouslySetInnerHTML: { __html: tableData } })
                )
            ]
        );
    },

    save: function () {
        return null; // Dynamic block does not need to be saved
    }
});
