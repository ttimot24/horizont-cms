export interface FilterOption {
    label: string;
    value: string;
    type?: 'text' | 'number' | 'date' | 'enum' | 'boolean';
    extra?: any; // For any additional data needed for specific types (e.g., enum options)
}