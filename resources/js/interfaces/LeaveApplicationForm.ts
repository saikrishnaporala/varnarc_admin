export interface LeaveApplicationForm {
    employeeId: string;       // Employee ID (e.g., "OPTY016")
    employeeName: string;     // Full name of the employee
    leaveType: string;        // Selected leave type (e.g., "Sick Leave", "Casual Leave")
    startDate: Date | null;   // Leave start date
    endDate: Date | null;     // Leave end date
    teamEmail: string;        // Email ID of the employee's team
    reason: string;           // Reason for leave
    approverId: string;       // Approver's ID
}