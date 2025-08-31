export interface Holiday {
    name: string;
    date: string; // Use string format for simplicity (e.g., "2024-12-25")
    description?: string; // Optional field
    icon?: string; // Optional icon field for icons like mdi-christmas-tree, etc.
}