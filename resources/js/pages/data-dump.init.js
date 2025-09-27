// /*
// Template Name: Velzon - Admin & Dashboard Template
// Author: Themesbrand
// Website: https://Themesbrand.com/
// Contact: Themesbrand@gmail.com
// File: CRM-contact Js File
// */

$(document).ready(function () {

    $('#showImport').on('click', function () {
        $('#importSection').slideToggle('fast'); 
    });

    $('#importurl').on('submit', function (e) {
        e.preventDefault();
    
        $.ajax({
            url: "/api/import/google-drive",  // ✅ updated route
            method: "POST",
            data: $(this).serialize(),
            beforeSend: function () {
                $('#importMessage').html('<div class="alert alert-info">Importing... please wait.</div>');
            },
            success: function (response) {
                if (response.status === 'success') {
                    $('#importMessage').html('<div class="alert alert-success">' + response.message + '</div>');
                    $('#importurl')[0].reset(); // reset form after success
                } else {
                    $('#importMessage').html('<div class="alert alert-warning">' + response.message + '</div>');
                }
            },
            error: function (xhr) {
                let errorMessage = 'Something went wrong!';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                $('#importMessage').html('<div class="alert alert-danger">' + errorMessage + '</div>');
            }
        });
    });

    function loadDriveFiles() {
        $.get("/api/datadump", function (data) {
            if (data.length === 0) {
                $(".noresult").show();
                return;
            }
            $(".noresult").hide();
    
            // Destroy if already initialized
            if ($.fn.DataTable.isDataTable("#driveFilesTable")) {
                $("#driveFilesTable").DataTable().clear().destroy();
            }
    
            // Initialize DataTable
            $("#driveFilesTable").DataTable({
                data: data,
                columns: [
                    {
                        data: "id",
                        render: function (id, type, row) {
                            return `
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="chk_child" value="${id}">
                                </div>`;
                        }
                    },
                    { data: "name", defaultContent: "" },
                    { 
                        data: "mime",
                        render: function (mime) {
                            return getShortMime(mime) ?? "";
                            // return mime;
                        }
                    },
                    { data: "status", defaultContent: "" },
                    { data: "table_name", defaultContent: "" },
                    {
                        data: null,
                        render: function (row) {
                            return `<div class="btn-group mt-4 mt-md-0" role="group" aria-label="Basic example">                                    
                                        <button class="btn btn-sm btn-primary view-item-btn" data-id="${row.id}"><i class="ri-eye-fill"></i></button>
                                        <button class="btn btn-sm btn-warning openLink" data-link="${row.id}"><i class=" ri-external-link-fill"></i></button>
                                        <button class="btn btn-sm btn-danger import-item-btn" data-id="${row.id}"><i class="ri-database-2-fill"></i></button>
                                    </div>
                                `;
                        }
                    }
                ],
                responsive: true,
                pageLength: 10, // show 10 rows per page
                order: [[1, "asc"]], // default sort by name
                // ✅ Highlight imported rows
                createdRow: function (row, data) {
                    if (data.status === "imported") {
                        $(row).addClass("table-success"); // Bootstrap green background
                    }
                }
            });
        });
    }    

    function getShortMime(mime) {
        const map = {
            "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet": "XLSX",
            "application/vnd.ms-excel": "XLS",
            "text/csv": "CSV",
            "application/pdf": "PDF",
            "application/vnd.google-apps.document": "Google Doc",
            "application/vnd.google-apps.spreadsheet": "Google Sheet",
            "application/vnd.google-apps.presentation": "Google Slide"
        };
    
        return map[mime] || mime; // fallback to full MIME if not mapped
    }
    
    loadDriveFiles();

    // Add or Update Contact
    $(".tablelist-form").on("submit", function (e) {
        e.preventDefault();

        let id = $("#id-field").val();
        let method = id ? "PUT" : "POST";
        let url = id ? `/api/contacts/${id}` : "/api/contacts";

        $.ajax({
            url: url,
            type: method,
            data: {
                name: $("#customername-field").val(),
                company_name: $("#company_name-field").val(),
                designation: $("#designation-field").val(),
                email: $("#email_id-field").val(),
                phone: $("#phone-field").val(),
                lead_score: $("#lead_score-field").val(),
                tags: $("#taginput-choices").val(),
            },
            success: function () {
                $("#showModal").modal("hide");
                loadContacts();
            },
            error: function (xhr) {
                Swal.fire("Error", xhr.responseText, "error");
            }
        });
    });

    // Import contact
    let importId;
    $(document).on("click", ".import-item-btn", function () {
        let $row = $(this).closest("tr"); // ✅ define the row
        importId = $(this).data("id");
        $.ajax({
            url: `/api/datadump/${importId}`,
            type: "Post",
            success: function (response) {
                // ✅ update status in the same row
                $row.find(".status").text("imported");

                // ✅ optionally set table name if returned
                if (response.table_name) {
                    $row.find(".table_name").text(response.table_name);
                }

                // ✅ remove Import option (since already imported)
                $row.find(".import-item-btn").closest("li").remove();
                Toastify({
                    text: "imported successfully!",
                    duration: 3000,
                    gravity: "top", // top or bottom
                    position: "right", // left, center, right
                    backgroundColor: "linear-gradient(to right, #00b09b, #96c93d)"
                }).showToast();
                loadDriveFiles();
            },
            error: function (xhr) {
                Swal.fire("Error", xhr.responseText, "error");
            }
        });
    });

    $(document).on("click", ".openLink", function () {
        let url = $(this).data('link');
        window.open(url, '_blank');
    });

    $(document).on('click', '.view-item-btn', function () {
        let tableName = $(this).data('id');

        // Fade out files list
        $('#filesTableWrapper').fadeOut('fast', function () {
            // Load table records via AJAX
            $.ajax({
                url: '/api/data/records/' + tableName, // Laravel route
                method: 'GET',
                success: function (response) {
                    // Set title
                    $('#recordsTitle').text("Records from: " + tableName);

                    // Destroy old DataTable if exists
                    if ($.fn.DataTable.isDataTable('#recordsTable')) {
                        $('#recordsTable').DataTable().destroy();
                        $('#recordsTable').empty();
                    }

                    // Build new DataTable
                    $('#recordsTable').DataTable({
                        data: response.data,
                        columns: [
                            ...response.columns.slice(1, -2), // all except "Actions"
                            {
                                data: null,
                                title: "Actions",
                                render: function (data, type, row) {
                                    return `
                                    <div class="btn-group mt-4 mt-md-0" role="group" aria-label="Basic example">                                    
                                        <button class="btn btn-sm btn-primary view-btn" data-id="${row.id}" data-table="${tableName}"><i class="ri-add-box-fill"></i></button>
                                        <button class="btn btn-sm btn-warning edit-btn" data-id="${row.id}" data-table="${tableName}"><i class="ri-pencil-fill"></i></button>
                                        <button class="btn btn-sm btn-danger delete-btn" data-id="${row.id}" data-table="${tableName}"><i class="ri-delete-bin-fill"></i></button>
                                    </div>
                                    `;
                                }
                            }
                        ],
                        pageLength: 10
                    });

                    // Show records
                    $('#recordsWrapper').fadeIn();
                }
            });
        });

        let from = tableName;
        let to = "contacts";

        $.getJSON(`/api/mapping/columns?from=${from}&to=${to}`, function(data) {
            let rows = '';
            data.from_columns.forEach(function(fromCol) {
                let selected = '';
                if (data.saved_mapping && data.saved_mapping[fromCol]) {
                    selected = data.saved_mapping[fromCol];
                }
    
                rows += `<tr>
                            <td>${fromCol}</td>
                            <td>
                                <select name="mapping[${fromCol}]" class="form-select">
                                    <option value="">-- Select --</option>`;
                data.to_columns.forEach(function(toCol) {
                    rows += `<option value="${toCol}" ${selected == toCol ? 'selected' : ''}>${toCol}</option>`;
                });
                rows += `</select></td></tr>`;
            });
            $('#mappingTable tbody').html(rows);
        });
    });

    // Back button → go back to files list
    $('#backBtn').on('click', function () {
        $('#recordsWrapper').fadeOut('fast', function () {
            $('#filesTableWrapper').fadeIn();
        });
    });

    $('#mappingForm').submit(function(e){
        e.preventDefault();
        let from = $('#fromTable').val();
        let to = $('#toTable').val();
        let mapping = {};
    
        $('#mappingTable select').each(function(){
            let key = $(this).attr('name').replace('mapping[','').replace(']','');
            let value = $(this).val();
            if(value) mapping[key] = value;
        });
    
        $.ajax({
            url: '/api/mapping/save',
            type: 'POST',
            data: {
                from_table: from,
                to_table: to,
                mapping: mapping,
                _token: '{{ csrf_token() }}'
            },
            success: function(res){
                alert('Mapping saved successfully!');
            }
        });
    });
});
