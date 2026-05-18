import { describe, expect, it, beforeEach } from 'vitest';
import ParentPageSelector from './ParentPageSelector';

describe('ParentPageSelector', () => {

    let component: any;

    beforeEach(() => {
        component = ParentPageSelector;
    });

    describe('Component Definition', () => {
        it('should have correct component name', () => {
            expect(component.name).toBe('ParentPageSelector');
        });
    });

});