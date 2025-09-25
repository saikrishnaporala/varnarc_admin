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

    // Load contacts into table
    // function loadDriveFiles() {
    //     $.get("/api/datadump", function (data) {
    //         let tbody = $("#driveFilesTable tbody");
    //         tbody.empty();

    //         if (data.length === 0) {
    //             $(".noresult").show();
    //             return;
    //         }
    //         $(".noresult").hide();

    //         data.forEach(dump => {
    //             tbody.append(`
    //                 <tr>
    //                     <td>
    //                         <div class="form-check">
    //                             <input class="form-check-input" type="checkbox" name="chk_child" value="${dump.id}">
    //                         </div>
    //                     </td>
    //                     <td class="name">${dump.name ?? ''}</td>
    //                     <td class="mime">${getShortMime(dump.mime) ?? ''}</td>
    //                     <td class="file_id">${dump.file_id ?? ''}</td>
    //                     <td class="status">${dump.status ?? ''}</td>
    //                     <td class="table_name">${dump.table_name ?? ''}</td>
    //                     <td>
    //                         <div class="dropdown">
    //                             <button class="btn btn-soft-secondary btn-sm dropdown" data-bs-toggle="dropdown">
    //                                 <i class="ri-more-fill align-middle"></i>
    //                             </button>
    //                             <ul class="dropdown-menu dropdown-menu-end">
    //                                 <li><a class="dropdown-item view-item-btn" href="javascript:void(0);" data-id="${dump.id}">
    //                                     <i class="ri-eye-fill align-bottom me-2 text-muted"></i> View</a></li>
    //                                 <li><a class="dropdown-item edit-item-btn" href="${dump.url ?? ''}" target="_blank">
    //                                     <i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Open</a></li>
    //                                 ${dump.status !== 'imported' ? `
    //                                     <li>
    //                                         <a class="dropdown-item import-item-btn" href="javascript:void(0);" data-id="${dump.id}">
    //                                             <i class="ri-database-2-fill align-bottom me-2 text-muted"></i> Import
    //                                         </a>
    //                                     </li>` : ''}
    //                             </ul>
    //                         </div>
    //                     </td>
    //                 </tr>
    //             `);
    //         });
    //     });
    // }

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
                            return `
                                <div class="dropdown">
                                    <button class="btn btn-soft-secondary btn-sm dropdown" data-bs-toggle="dropdown">
                                        <i class="ri-more-fill align-middle"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a class="dropdown-item view-item-btn" href="javascript:void(0);" data-id="${row.table_name}">
                                                <i class="ri-eye-fill align-bottom me-2 text-muted"></i> View
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item edit-item-btn" href="${row.url ?? ''}" target="_blank">
                                                <i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Open
                                            </a>
                                        </li>
                                        
                                            <li>
                                                <a class="dropdown-item import-item-btn" href="javascript:void(0);" data-id="${row.id}">
                                                    <i class="ri-database-2-fill align-bottom me-2 text-muted"></i> Import
                                                </a>
                                            </li>
                                    </ul>
                                </div>`;
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

    $(document).on('click', '.view-item-btn', function () {
        let tableName = $(this).data('id');

        // Fade out files list
        $('#filesTableWrapper').fadeOut('fast', function () {
            // Load table records via AJAX
            $.ajax({
                url: '/data/records/' + tableName, // Laravel route
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
                        columns: response.columns,
                        pageLength: 10
                    });

                    // Show records
                    $('#recordsWrapper').fadeIn();
                }
            });
        });
    });

    // Back button → go back to files list
    $('#backBtn').on('click', function () {
        $('#recordsWrapper').fadeOut('fast', function () {
            $('#filesTableWrapper').fadeIn();
        });
    });
});
