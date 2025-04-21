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

        if (!props.attributes.uniqueId) {
            setAttributes({ uniqueId: 'block-' + Date.now() });
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
                        // Create a temporary container for parsing the HTML string
                        const tempDiv = document.createElement('div');
                        tempDiv.innerHTML = data.data.html;

                        const loadingText = tempDiv.querySelector('.akash-api-plugin-table-container .loading-text');

                        if (loadingText) {
                            loadingText.remove();
                        }

                        const tableContainer = tempDiv.querySelector('.akash-api-plugin-table-container .akash-api-plugin-table-content');

                        if (tableContainer) {
                            // Fetch additional table content
                            jQuery.ajax({
                                url: akashApiPluginData.ajaxUrl +
                                    '?action=' + encodeURIComponent(akashApiPluginData.action1) +
                                    '&security=' + encodeURIComponent(akashApiPluginTableData.nonce) +
                                    '&id=' + encodeURIComponent(attributes.showId) +
                                    '&fname=' + encodeURIComponent(attributes.showFirstName) +
                                    '&lname=' + encodeURIComponent(attributes.showLastName) +
                                    '&email=' + encodeURIComponent(attributes.showEmail) +
                                    '&date=' + encodeURIComponent(attributes.showDate),
                                method: 'GET',
                                dataType: 'json',
                                success: function (response) {
                                    if (response.success) {
                                        tableContainer.innerHTML = response.data.html;
                                    } else {
                                        tableContainer.innerHTML = akashApiPluginData.errorText;
                                    }

                                    // Finally, set the updated HTML as the block content
                                    setTableData(tempDiv.innerHTML);
                                },
                                error: function () {
                                    tableContainer.innerHTML = akashApiPluginData.errorText;
                                    setTableData(tempDiv.innerHTML);
                                }
                            });
                        } else {
                            setTableData(data.data.html);
                        }
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
                            label: "Show ID Column",
                            checked: attributes.showId,
                            onChange: (value) => setAttributes({ showId: value })
                        }),
                        createElement(ToggleControl, {
                            label: "Show First Name Column",
                            checked: attributes.showFirstName,
                            onChange: (value) => setAttributes({ showFirstName: value })
                        }),
                        createElement(ToggleControl, {
                            label: "Show Last Name Column",
                            checked: attributes.showLastName,
                            onChange: (value) => setAttributes({ showLastName: value })
                        }),
                        createElement(ToggleControl, {
                            label: "Show Email Column",
                            checked: attributes.showEmail,
                            onChange: (value) => setAttributes({ showEmail: value })
                        }),
                        createElement(ToggleControl, {
                            label: "Show Date Column",
                            checked: attributes.showDate,
                            onChange: (value) => setAttributes({ showDate: value })
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
