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
                        }
                    },
                    { data: "file_id", defaultContent: "" },
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
                                            <a class="dropdown-item view-item-btn" href="javascript:void(0);" data-id="${row.id}">
                                                <i class="ri-eye-fill align-bottom me-2 text-muted"></i> View
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item edit-item-btn" href="${row.url ?? ''}" target="_blank">
                                                <i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Open
                                            </a>
                                        </li>
                                        ${row.status !== 'imported' ? `
                                            <li>
                                                <a class="dropdown-item import-item-btn" href="javascript:void(0);" data-id="${row.id}">
                                                    <i class="ri-database-2-fill align-bottom me-2 text-muted"></i> Import
                                                </a>
                                            </li>` : ""}
                                    </ul>
                                </div>`;
                        }
                    }
                ],
                responsive: true,
                pageLength: 10, // show 10 rows per page
                order: [[1, "asc"]] // default sort by name
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
        importId = $(this).data("id");
        $.ajax({
            url: `/api/datadump/${importId}`,
            type: "Post",
            success: function () {
                $("#deleteRecordModal").modal("hide");
                loadContacts();
            },
            error: function (xhr) {
                Swal.fire("Error", xhr.responseText, "error");
            }
        });
    });
});
