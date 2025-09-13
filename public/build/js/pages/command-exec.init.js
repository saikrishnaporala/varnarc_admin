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

    // Load contacts into table
    function loadCommandLogs() {
        $.get('/api/command-logs', function(data) {
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

    loadCommandLogs();

});
