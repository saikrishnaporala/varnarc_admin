// /*
// Template Name: Velzon - Admin & Dashboard Template
// Author: Themesbrand
// Website: https://Themesbrand.com/
// Contact: Themesbrand@gmail.com
// File: CRM-contact Js File
// */

$(document).ready(function () {

    $('#execCommand').on('submit', function(e) {
        e.preventDefault();
    
        $.ajax({
            url: '/api/execute-command',
            type: 'POST',
            data: $(this).serialize(),
            success: function(res) {
                $('#messages').html('<pre>' + res.output + '</pre>');
            },
            error: function(xhr) {
                $('#messages').html('<span class="text-danger">Error: ' + xhr.responseText + '</span>');
            }
        });
    });

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
    function loadContacts() {
        $.get("/api/contacts", function (data) {
            let tbody = $("#customerTable tbody");
            tbody.empty();

            if (data.length === 0) {
                $(".noresult").show();
                return;
            }
            $(".noresult").hide();

            data.forEach(contact => {
                tbody.append(`
                    <tr>
                        <td>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="chk_child" value="${contact.id}">
                            </div>
                        </td>
                        <td class="name">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <img src="${contact.image_url ?? '/build/images/users/user-dummy-img.jpg'}"
                                        alt="" class="avatar-xs rounded-circle">
                                </div>
                                <div class="flex-grow-1 ms-2">${contact.name}</div>
                            </div>
                        </td>
                        <td class="company_name">${contact.company_name ?? ''}</td>
                        <td class="email_id">${contact.email}</td>
                        <td class="phone">${contact.phone ?? ''}</td>
                        <td class="lead_score">${contact.lead_score ?? ''}</td>
                        <td class="tags">${contact.tags ?? ''}</td>
                        <td class="date">${contact.updated_at ?? ''}</td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-soft-secondary btn-sm dropdown" data-bs-toggle="dropdown">
                                    <i class="ri-more-fill align-middle"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item view-item-btn" href="javascript:void(0);" data-id="${contact.id}">
                                        <i class="ri-eye-fill align-bottom me-2 text-muted"></i> View</a></li>
                                    <li><a class="dropdown-item edit-item-btn" href="javascript:void(0);" data-id="${contact.id}">
                                        <i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Edit</a></li>
                                    <li><a class="dropdown-item remove-item-btn" href="javascript:void(0);" data-id="${contact.id}">
                                        <i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i> Delete</a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                `);
            });
        });
    }

    loadContacts();

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

    // Edit contact
    $(document).on("click", ".edit-item-btn", function () {
        let id = $(this).data("id");
        $.get(`/api/contacts/${id}`, function (contact) {
            $("#id-field").val(contact.id);
            $("#customername-field").val(contact.name);
            $("#company_name-field").val(contact.company_name);
            $("#designation-field").val(contact.designation);
            $("#email_id-field").val(contact.email);
            $("#phone-field").val(contact.phone);
            $("#lead_score-field").val(contact.lead_score);
            $("#taginput-choices").val(contact.tags).trigger("change");

            $("#exampleModalLabel").text("Edit Contact");
            $("#add-btn").text("Update Contact");
            $("#showModal").modal("show");
        });
    });

    // Delete contact
    let deleteId;
    $(document).on("click", ".remove-item-btn", function () {
        deleteId = $(this).data("id");
        $("#deleteRecordModal").modal("show");
    });

    $("#delete-record").click(function () {
        $.ajax({
            url: `/api/contacts/${deleteId}`,
            type: "DELETE",
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
