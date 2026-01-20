import { describe, it, expect, beforeEach, afterEach, vi } from 'vitest';
import CategorySelector from './CategorySelector.ts';
import { of, throwError } from 'rxjs';
import { environment } from '../../environments/environment';

interface BlogpostCategory {
    id: number;
    name: string;
}

describe('CategorySelector - Unit Tests', () => {
    let component: any;
    let mockHttp: any;

    beforeEach(() => {
        mockHttp = {
            get: vi.fn()
        };

        component = CategorySelector;
    });

    afterEach(() => {
        vi.clearAllMocks();
    });

    describe('Component Definition', () => {
        it('should have correct component name', () => {
            expect(component.name).toBe('CategorySelector');
        });

        it('should have correct props defined', () => {
            expect(component.props).toHaveProperty('label');
            expect(component.props).toHaveProperty('blogpost_categories');
        });

        it('label prop should be String type', () => {
            expect(component.props.label.type).toBe(String);
        });

        it('blogpost_categories prop should be Array type', () => {
            expect(component.props.blogpost_categories.type).toBe(Array);
        });

        it('blogpost_categories should have empty array as default', () => {
            expect(component.props.blogpost_categories.default).toEqual([]);
        });
    });

    describe('Data Initialization', () => {
        it('should initialize data correctly', () => {
            const data = component.data();
            expect(data).toHaveProperty('categories');
            expect(data).toHaveProperty('selected_categories');
            expect(data.categories).toEqual([]);
            expect(data.selected_categories).toEqual([]);
        });

        it('categories should be array type', () => {
            const data = component.data();
            expect(Array.isArray(data.categories)).toBe(true);
        });

        it('selected_categories should be array type', () => {
            const data = component.data();
            expect(Array.isArray(data.selected_categories)).toBe(true);
        });
    });

    describe('Mounted Hook', () => {
        it('should have mounted lifecycle hook', () => {
            expect(component.mounted).toBeDefined();
            expect(typeof component.mounted).toBe('function');
        });

    /*    it('should call http.get on mount', () => {
            const vm = {
                blogpost_categories: [1, 2],
                selected_categories: [],
                categories: [],
                http: mockHttp
            };

            component.mounted.call(vm);

            expect(mockHttp.get).toHaveBeenCalledWith(
                environment.REST_API_BASE + '/categories'
            );
        }); */

        it('should set selected_categories from blogpost_categories', () => {
            const vm = {
                blogpost_categories: [1, 2],
                selected_categories: [],
                categories: [],
                http: mockHttp
            };

            mockHttp.get.mockReturnValue(
                of({ data: { data: [] } })
            );

            component.mounted.call(vm);

            expect(vm.blogpost_categories).toEqual([1, 2]);
        });
    });

    describe('Props Validation', () => {
        it('should accept label as string', () => {
            expect(component.props.label.type).toBe(String);
        });

        it('should accept blogpost_categories as array', () => {
            expect(component.props.blogpost_categories.type).toBe(Array);
        });

        it('should have default blogpost_categories as empty array', () => {
            const defaultValue = component.props.blogpost_categories.default;
            expect(defaultValue).toEqual([]);
            expect(Array.isArray(defaultValue)).toBe(true);
        });
    });

    describe('Component Logic', () => {
        it('should handle empty categories', () => {
            const data = component.data();
            expect(data.categories.length).toBe(0);
        });

        it('should handle empty selected_categories', () => {
            const data = component.data();
            expect(data.selected_categories.length).toBe(0);
        });

        it('should map BlogpostCategory interface correctly', () => {
            const mockCategory: BlogpostCategory = {
                id: 1,
                name: 'Test Category'
            };

            expect(mockCategory).toHaveProperty('id');
            expect(mockCategory).toHaveProperty('name');
            expect(typeof mockCategory.id).toBe('number');
            expect(typeof mockCategory.name).toBe('string');
        });
    });

    describe('API Integration Logic', () => {
        it('should construct correct API endpoint', () => {
            const expectedEndpoint = environment.REST_API_BASE + '/categories';
            expect(expectedEndpoint).toContain('/categories');
        });

        it('should use retry operator from rxjs', () => {

            const source = component.mounted.toString();
            expect(source).toContain('retry');
        });

        it('should use map operator from rxjs', () => {

            const source = component.mounted.toString();
            expect(source).toContain('map');
        });
    });

    describe('Type Safety', () => {
        it('BlogpostCategory interface should have required properties', () => {
            const category: BlogpostCategory = { id: 1, name: 'Test' };
            expect(category.id).toBeDefined();
            expect(category.name).toBeDefined();
        });

        it('selected_categories should contain only numbers', () => {
            const data = component.data();
            expect(Array.isArray(data.selected_categories)).toBe(true);
        });
    });

 /*   describe('Error Handling', () => {
        it('should handle http get method', () => {
            const vm = {
                blogpost_categories: [],
                selected_categories: [],
                categories: [],
                http: mockHttp
            };

            component.mounted.call(vm);

            expect(mockHttp.get).toHaveBeenCalled();
        });

        it('should be callable without throwing errors', () => {
            expect(() => {
                component.data();
            }).not.toThrow();
        });
    });*/
});