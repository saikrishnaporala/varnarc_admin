export interface EmployeeGrade {
    id: number | null;
    grade_name: string;
    min_salary: number;
    max_salary: number;
    benefits: string | null;
    promotion_criteria: string | null;
    created_at: Date | null;
    updated_at: Date | null;
  }