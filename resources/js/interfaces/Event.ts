export interface Event {
    id: number | string;
    title: string;
    start: Date | string;
    end?: Date | string;
    allDay?: boolean;
    className?: string;
    location?: string;
    extendedProps?: {
        department?: string;
        employeeId?: number;
        checkIn?: string;
        checkOut?: string;
    };
    description?: string;
    url?: string;
}