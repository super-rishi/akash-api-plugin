function akash_api_plugin_fetch_table_data(attributes) {
    console.log(attributes);
    const parent = document.getElementById(attributes.uniqueId);
    if (!parent) return;

    const loadingText = parent.querySelector('.akash-api-plugin-table-container .loading-text');
    const tableContainer = parent.querySelector('.akash-api-plugin-table-container .akash-api-plugin-table-content');

    if (loadingText) {
        loadingText.style.display = 'none';
    }

    if (tableContainer) {
        jQuery.ajax({
            url: akashApiPluginTableData.ajaxUrl +
                '?action=' + encodeURIComponent(akashApiPluginTableData.action) +
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
                    tableContainer.innerHTML = akashApiPluginTableData.errorText;
                }
            },
            error: function () {
                tableContainer.innerHTML = akashApiPluginTableData.errorText;
            }
        });
    }
}
