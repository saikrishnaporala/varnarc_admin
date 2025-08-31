import { ref } from "vue";

export interface ActivityLog {
    time: string;
    action: string;
}

export interface Attendance {
    id: number;
    employee_id: number;
    check_in: string;
    check_out: string;
    ms_worked: string | null;
    created_at: string;
    updated_at: string;
}

export interface WorkDay {
    day: string;
    hours: number;
}

export const WorkHoursDayWise = ref<WorkDay[]>([
  { day: 'Monday', hours: 8 },
  { day: 'Tuesday', hours: 8 },
  { day: 'Wednesday', hours: 8 },
  { day: 'Thursday', hours: 8 },
  { day: 'Friday', hours: 8 },
  { day: 'Saturday', hours: 4 },
  { day: 'Sunday', hours: 0 }
]);