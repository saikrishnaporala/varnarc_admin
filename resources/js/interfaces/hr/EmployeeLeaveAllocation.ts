import { LeaveAllocation } from "./LeaveAllocation";

export interface EmployeeLeaveAllocation {
    employeeId: string;
    employeeName: string;
    leaveAllocations: LeaveAllocation[];
}