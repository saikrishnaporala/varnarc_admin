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

    // Load commands into table
    function loadCommandLogs() {
        $.get('/api/command-logs', function(data) {
            let tbody = $("#customerTable tbody");
            tbody.empty();

            if (data.length === 0) {
                $(".noresult").show();
                return;
            }
            $(".noresult").hide();

            data.forEach(commands => {
                tbody.append(`
                    <tr>
                        <td class="company_name">${commands.command ?? ''}</td>
                        <td class="email_id">${commands.user_id}</td>
                        <td class="phone">${commands.created_at}</td>
                    </tr>
                `);
            });
        });
    }

    loadCommandLogs();

});
